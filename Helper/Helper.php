<?php

namespace unDosTres\paymentGateway\Helper;

use unDosTres\paymentGateway\Gateway\Config\Config;
use unDosTres\paymentGateway\PrivateConfig;
use \Magento\Framework\App\Action\Context;
use \Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use UDT\SDK\SDK;

/* PHP CLASS WHIT COMMON FUNCTIONS */

class Helper
{
    /* CONST TO SET MESSAGE ON FRONTEND -> addFrontMesage */
    const MSG_SUCCESS = 'MSG_SUCCESS';
    const MSG_WARNING = 'MSG_WARNING';
    const MSG_ERROR = 'MSG_ERROR';

    /* CONST TO SET MESSAGE ON FRONTEND -> callUdtSdk */
    const PAYMENT = 'payment';
    const REFUND = 'refund';
    const CANCEL = 'cancel';

    private $_logger;
    private $_orderFactory;
    private $_session;
    private $_messager;
    private $_storeManager;

    public function __construct(Context $context, LoggerInterface $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->_messager = $context->getMessageManager();
        $this->_logger = $logger;
        $this->_session = $session;
        $this->_orderFactory = $orderFactory;
        $this->_storeManager = $storeManager;
    }

    /* LOG INTO MAGENTO LOGS */
    public function log($message)
    {
        if (Config::UDT_APP_LOG === true) $this->_logger->info('UDT PAYMENT LOG: ' . $message);
    }

    /* ADD MESSAGE ON MAGENTO FRONTEND */
    public function addFrontMesage($type, $msg)
    {
        if ($type === Helper::MSG_SUCCESS) $this->_messager->addSuccess(__($msg));
        else if ($type === Helper::MSG_WARNING) $this->_messager->addWarning(__($msg));
        else if ($type === Helper::MSG_ERROR) $this->_messager->addError(__($msg));
    }

    /* GET ORDER FROM MAGENTO */
    public function getOrder()
    {
        $orderId = $this->_session->getLastRealOrderId();
        if (!isset($orderId)) return null;
        $order = $this->_orderFactory->create()->loadByIncrementId($orderId);
        if (!$order->getId()) return null;
        return $order;
    }

    /* RESTORE CART */
    public function restoreCart()
    {
        return $this->_session->restoreQuote();
    }

    /* REDIRECT USING JS */
    public function redirectPage($redirectUrl)
    {
        echo "<script>window.location.replace('$redirectUrl');</script>";
    }

    /* MONEY FORMAT */
    public function moneyFormat($money)
    {
        return floatval(number_format($money, 2, '.', ''));
    }

    /* GET LANDING URL WHEN UDT REDIRECTS */
    public function getReturnUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'unDosTres/checkout/redirect';
    }

    /* GET CALLBACK URL */
    public function getCallbackUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'rest/V2/custom/custom-api';
    }

    /* TRANSFORM ORDER TO JSON ORDER UDT */
    public function getAllItems($order): array
    {
        $items = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $items[] = array(
                'id' => (string)$item->getItemId(),
                'name' => $item->getName(),
                'price' => $this->moneyFormat($item->getPrice()),
                'quantity' => (int)$item->getQtyOrdered(),
                'discount' => 0,
                'variation_id' => (string)0, /* TODO check this val*/
            );
        }
        return $items;
    }

    /* GET THE JSON THAT IS SENDED TO UDT */
    public function getOrderJSON($order)
    {
        $shippingAddress = $order->getShippingAddress();
        $shippingAddressParts = preg_split('/\r\n|\r|\n/', $shippingAddress->getData('street'));
        $orderId = $order->getRealOrderId();
        $data = [
            'currency' => $order->getOrderCurrencyCode(),
            'callbackUrl' => $this->getCallbackUrl(),
            'returnUrl' => $this->getReturnUrl(),
            'reference' => (string)$orderId,
            'transactionId' => (string)$orderId,
            'paymentId' => (string)$orderId,
            'orderId' => (string)$orderId,
            'value' => $this->moneyFormat($order->getTotalDue()),
            'installments' => 0,
            'paymentMethod' => "UnDosTres",
            'miniCart' => [
                "buyer" => [
                    "firstName" => $order->getCustomerFirstname(),
                    "lastName" => $order->getCustomerLastname(),
                    'email' => $order->getData('customer_email'),
                    'phone' => $order->getBillingAddress()->getData('telephone')
                ],
                "taxValue" => $this->moneyFormat($order->getTaxAmount()),
                "shippingValue" => $this->moneyFormat($order->getShippingAmount()),
                "shippingAddress" => [
                    'street' => $shippingAddressParts[0],
                    'city' => $shippingAddress->getData('city'),
                    'state' => $shippingAddress->getData('region'),
                    'postalCode' => $shippingAddress->getData('postcode')
                ],
                "items" => $this->getAllItems($order)
            ]
        ];
        return $data;
    }

    /* GENERETE DE URL TO PAY ON UDT */
    public function getPaymentUrl($json)
    {
        $response = $this->callUdtSdk(Helper::PAYMENT, $json);
        if ($response === null) return null;
        $redirect = urlencode($response["body"]["queryParams"]["url"]);
        $base = 'undostres://home?stage=superAppPaymentIntent&url=';
        return ($base . $redirect);
    }

    /* CANCEL ORDER */
    public function cancelOrder($order)
    {
        if ($order !== null) $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED)->save();
    }

    /* CHECK IS ORDER PENDING */
    public function isOrderPending($order)
    {
        return $order !== null && $order->getState() === Order::STATE_PENDING_PAYMENT;
    }

    /* CHECK IS ORDER CENCELED */
    public function isOrderCanceled($order)
    {
        return $order !== null && $order->getState() === Order::STATE_CANCELED;
    }

    /* CHECK IS ORDER PROCESING */
    public function isOrderProcesing($order)
    {
        return $order !== null && $order->getState() === Order::STATE_PROCESSING;
    }

    /* 
    SDK INTERFACE 
    type: 'payment','refund','cancel'
    request: body
    */
    public function callUdtSdk($type, $request)
    {
        $request_json = json_encode(array($type => $request));
        $this->log(sprintf('Request sent to UnDosTres with the SDK for %s: %s', $type, $request_json));
        $sdk = new SDK(PrivateConfig::SDK);
        $response = $sdk->handlePayload($request_json);
        $this->log(sprintf('Request receive of UnDosTres with the SDK for %s: %s', $type, json_encode($response)));
        if ($response['code'] !== 200) return null;
        if ($type === 'payment' && Config::UDT_APP_ENVIRONMENT == 'localhost') {
            $response["body"]["queryParams"]["url"] = str_replace("https://test.undostres.com.mx", "http://localhost:8081", $response["body"]["queryParams"]["url"]);
            $this->log('Payment url update to localhost: %s', $response["body"]["queryParams"]["url"]);
        }
        return $response;
    }
}
