<?php

namespace Undostres\PaymentGateway\Helper;

use Exception;
use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Undostres\PaymentGateway\Logger\Logger;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;

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
    protected $order;
    protected $session;
    protected $orderSender;
    protected $messageManager;
    protected $storeManager;
    protected $gatewayConfig;
    protected $invoice;
    protected $transaction;

    public function __construct(Config $gatewayConfig, Context $context, Logger $logger, Session $session, Order $order, StoreManagerInterface $storeManager, OrderSender $orderSender, InvoiceService $invoice, Transaction $transaction)
    {
        $this->messageManager = $context->getMessageManager();
        $this->logger = $logger;
        $this->session = $session;
        $this->order = $order;
        $this->storeManager = $storeManager;
        $this->gatewayConfig = $gatewayConfig;
        $this->orderSender = $orderSender;
        $this->invoice = $invoice;
        $this->transaction = $transaction;
        SASDK::init($this->gatewayConfig->getKey(), $this->gatewayConfig->getUrl());
    }

    /* LOG TO UDT FILE */
    public function log(string $message, int $type = self::LOG_DEBUG)
    {
        $message = "\n" . '========= UDT LOG =========' . "\n" . $message . "\n" . '========= UDT END =========  ==>  ';
        if ($this->gatewayConfig->canLog()) {
            if ($type === self::LOG_DEBUG) $this->logger->info($message);
            else if ($type === self::LOG_WARNING) $this->logger->warning($message);
            else if ($type === self::LOG_ERROR) $this->logger->critical($message);
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

    /* RESPOND JSON USING HEADER */
    public function responseJSON($json, int $code = 200, string $msg = "")
    {
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';
        echo json_encode($json);
        header($protocol . ' ' . $code . ' ' . $msg);
        die();
    }

    /* GET HEADERS OF PETITION */
    /**
     * @throws Exception
     */
    public function areValidHeaders(): bool
    {
        $serverHeaders = apache_request_headers();
        $headers = array();
        foreach ($serverHeaders as $header => $value) {
            $headers[strtolower($header)] = $value;
        }
        return SASDK::validateRequestHeaders($headers["x-vtex-api-appkey"], $headers["x-vtex-api-apptoken"]);
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
        return $this->storeManager->getStore()->getBaseUrl() . 'rest/V1/udt/callback';
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
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() . 'checkout/onepage/success');
    }

    /* REDIRECT TO CART PAGE */
    public function redirectToCheckoutCart()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() . 'checkout/cart');
    }

    /* GET ORDER FROM MAGENTO */
    public function getOrder($orderId = null)
    {
        if ($orderId === null) $orderId = $this->session->getLastRealOrderId();
        if (!isset($orderId)) return null;
        $order = $this->order->loadByIncrementId($orderId);
        $id = $order->getId();
        if (!$order->getId()) return null;
        return $order;
    }

    public function createPayment($json)
    {
        $response = SASDK::createPayment($json);
        if ($response["code"] !== 200) return null;
        return $response["response"];
    }

    /* GET ORDER FROM MAGENTO */
    /**
     * @throws Exception
     */
    public function processOrder($paymentId, $status): array
    {
        $order = $this->getOrder($paymentId);
        if ($order === null) return ['code' => 404, 'message' => 'Orden no encontrada.'];
        switch ($status) {
            case 'approved':
                if ($this->isOrderPending($paymentId)) {
                    $this->invoiceOrder($order, $paymentId);
                    $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING)->setIsCustomerNotified(true);
                    $this->orderSender->send($order);
                    $response = [
                        'code' => 200,
                        'message' => 'User Paid.',
                        'paymentId' => (string)$paymentId,
                        'status' => $order->$order->getState()
                    ];
                } else {
                    $response = [
                        'code' => 400,
                        'message' => 'Not valid order status.',
                        'paymentId' => (string)$paymentId,
                        'status' => $order->$order->getState()
                    ];
                }
                break;
            case 'denied':
                $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                $response = [
                    'code' => 200,
                    'message' => 'Order cancel successfully.',
                    'paymentId' => (string)$paymentId,
                    'status' => Order::STATE_CANCELED
                ];
                break;
            default:
                $response = [
                    'code' => 400,
                    'message' => 'Bad request',
                    'paymentId' => (string)$paymentId,
                    'status' => $order->getState()
                ];
                break;
        }
        $order->save();
        return $response;
    }

    /**
     * @throws Exception
     */
    private function invoiceOrder($order, $transactionId)
    {
        if (!$order->canInvoice()) throw new Exception('Cannot create an invoice.');
        $invoice = $this->invoice->prepareInvoice($order);
        if (!$invoice->getTotalQty()) throw new Exception('No se puede realizar un pago sin productos.');
        $invoice->setTransactionId($transactionId);
        $invoice->setRequestedCaptureCase(Order\Invoice::CAPTURE_ONLINE);
        $invoice->setState(Order\Invoice::STATE_PAID);
        $invoice->register();
        $invoice->pay();
        $transaction = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
        $transaction->save();
    }
}
