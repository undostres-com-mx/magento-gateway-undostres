<?php

namespace Undostres\PaymentGateway\Helper;

use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Gateway\Config;
use \Magento\Framework\App\Action\Context;
use \Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use \Magento\Checkout\Model\Session;
use \Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/* PHP CLASS WHIT COMMON FUNCTIONS */

class HelperOld
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
        if ($type === HelperOld::MSG_SUCCESS) $this->_messager->addSuccess(__($msg));
        else if ($type === HelperOld::MSG_WARNING) $this->_messager->addWarning(__($msg));
        else if ($type === HelperOld::MSG_ERROR) $this->_messager->addError(__($msg));
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
        header('Location: ' . $redirectUrl);
        die();
        //die("<script>window.location.replace('$redirectUrl');</script>");
    }

    /* MONEY FORMAT */
    public function moneyFormat($money)
    {
        return floatval(number_format($money, 2, '.', ''));
    }

    /* GET LANDING URL WHEN UDT REDIRECTS */
    public function getReturnUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl() . 'Undostres/checkout/redirect';
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
        SASDK::init('36wqV4OcrAa1/Sq9LJ7ARcclXqRhBJsVTZEFR4eo8Htxn6o4nKPrfpW/9rmP3SxPMNCSIfel+507CLU1HIknbSq242/YXNeun/Kwyhqp47LqdiSEUrlwNhBezHSiQwjx6c58W0NUne+IvfKl255TE4qn5Upf1AYoo4CzWClNkfN4vftn/FNOTahWZR6nL46IkzhQqTbNkWDjApP3NXhiBpVaUsci1f9JXaC9WlMR4mWV1FsghFgvPSpCUac+1T/O+pdkHORk0borVbQqBtzox+iZlqkgwjy2TyBpIVwgDhVer5IwhzSaA6Bz4uWULpPMIf3nAqtxShmNwNCnAX5Z1lPrhSdH3j+5hClk47kWCkqHU7sGC+LllD2yOeZtD5YFp2BHdAmlNJHh0p5EClLbcryWaYRRSiOOgZWC7zObOVU=', 'https://nobugs.undostres.com.mx');
        $response = SASDK::createPayment($json);
        $this->log(sprintf('Request receive of UnDosTres with the SDK for %s: %s', json_encode($json), json_encode($response)));
        if ($response['code'] !== 200) return null;
        return $response['response'];
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
        SASDK::init('36wqV4OcrAa1/Sq9LJ7ARcclXqRhBJsVTZEFR4eo8Htxn6o4nKPrfpW/9rmP3SxPMNCSIfel+507CLU1HIknbSq242/YXNeun/Kwyhqp47LqdiSEUrlwNhBezHSiQwjx6c58W0NUne+IvfKl255TE4qn5Upf1AYoo4CzWClNkfN4vftn/FNOTahWZR6nL46IkzhQqTbNkWDjApP3NXhiBpVaUsci1f9JXaC9WlMR4mWV1FsghFgvPSpCUac+1T/O+pdkHORk0borVbQqBtzox+iZlqkgwjy2TyBpIVwgDhVer5IwhzSaA6Bz4uWULpPMIf3nAqtxShmNwNCnAX5Z1lPrhSdH3j+5hClk47kWCkqHU7sGC+LllD2yOeZtD5YFp2BHdAmlNJHh0p5EClLbcryWaYRRSiOOgZWC7zObOVU=', 'https://nobugs.undostres.com.mx');
        $this->log(sprintf('Request sent to UnDosTres with the SDK for %s: ', $type));
        $response = SASDK::createPayment($request);
        $this->log(sprintf('Request receive of UnDosTres with the SDK for %s: %s', $type, json_encode($response)));
        if ($response['code'] !== 200) return null;
        return $response;
    }
}