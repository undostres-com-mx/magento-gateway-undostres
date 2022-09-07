<?php

namespace Undostres\PaymentGateway\Helper;

use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Framework\Logger\Monolog;
use Undostres\PaymentGateway\Logger\Logger;

/* HELPER CLASS, LOG, FRONT MESSAGE, SDK COMMUNICATION */

class Helper
{
    /* CONST TO SET MESSAGE ON FRONTEND */
    const MSG_SUCCESS = 'MSG_SUCCESS';
    const MSG_WARNING = 'MSG_WARNING';
    const MSG_ERROR = 'MSG_ERROR';

    /* CONST TO SET DEBUG LEVEL */
    const LOG_DEBUG = 0;
    const LOG_WARNING = 1;
    const LOG_ERROR = 2;

    /* CLASS VARIABLES */
    protected $logger;
    protected $orderFactory;
    protected $session;
    protected $messageManager;
    protected $storeManager;
    protected $gatewayConfig;

    public function __construct(Config $gatewayConfig, Context $context, Logger $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->messageManager = $context->getMessageManager();
        $this->logger = $logger;
        $this->session = $session;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
        $this->gatewayConfig = $gatewayConfig;
        SASDK::init($this->gatewayConfig->getKey(), $this->gatewayConfig->getUrl());
    }

    /* LOG TO UDT FILE */
    public function log(string $message, int $type = self::LOG_DEBUG)
    {
        $message = "\n" . '========= UDT LOG =========' . "\n" . $message . "\n" . '========= UDT END =========  ==>  ';
        if ($this->gatewayConfig->canLog()) {
            if($type=== self::LOG_DEBUG) $this->logger->info($message);
            else if($type=== self::LOG_WARNING) $this->logger->warning($message);
            else if($type=== self::LOG_ERROR) $this->logger->critical($message);
        }
    }

    /* ADD MESSAGE ON MAGENTO FRONTEND */
    public function addFrontMessage($type, $msg)
    {
        if ($type === self::MSG_SUCCESS) $this->messageManager->addSuccess(__($msg));
        else if ($type === self::MSG_WARNING) $this->messageManager->addWarning(__($msg));
        else if ($type === self::MSG_ERROR) $this->messageManager->addError(__($msg));
    }

    /* CHECK IS ORDER PENDING */
    public function isOrderPending($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_PENDING_PAYMENT;
    }

    /* CHECK IS ORDER CANCELED */
    public function isOrderCanceled($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_CANCELED;
    }

    /* CHECK IS ORDER PROCESSING */
    public function isOrderProcessing($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_PROCESSING;
    }

    /* REDIRECT USING HEADER */
    public function redirectPage($redirectUrl)
    {
        header('Location: ' . $redirectUrl);
        die();
    }

    /* GET THE JSON THAT IS SENT TO UDT */
    public function getOrderJSON($order): array
    {
        $shippingAddress = $order->getShippingAddress();
        $shippingAddressParts = preg_split('/\r\n|\r|\n/', $shippingAddress->getData('street'));
        $orderId = $order->getRealOrderId();
        return [
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
    }

    /* GET CALLBACK URL */
    public function getCallbackUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() .'rest/V1/udt/callback';
    }

    /* GET LANDING URL WHEN UDT REDIRECTS */
    public function getReturnUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'rest/V1/udt/redirect';
    }

    /* MONEY FORMAT ####.## */
    public function moneyFormat($money): float
    {
        return floatval(number_format($money, 2, '.', ''));
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
                'variation_id' => "0",
            );
        }
        return $items;
    }

    /* RESTORE CART */
    public function restoreCart()
    {
        return $this->session->restoreQuote();
    }

    /* CANCEL ORDER */
    public function cancelOrder($order)
    {
        if ($order !== null) $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED)->save();
    }

    /* REDIRECT TO SUCCESS PAGE */
    public function redirectToCheckoutOnePageSuccess()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() .'checkout/onepage/success');
    }

    /* REDIRECT TO CART PAGE */
    public function redirectToCheckoutCart()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() .'checkout/cart');
    }

    /* GENERATE THE URL TO PAY ON UDT */
    public function createPayment($json)
    {
        $response = SASDK::createPayment($json);
        $this->log(sprintf('Request receive of UnDosTres with the SDK for %s: %s', json_encode($json), json_encode($response)));
        if ($response['code'] !== 200) return null;
        return $response['response'];
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
}
