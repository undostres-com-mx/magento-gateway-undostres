<?php

namespace Undostres\PaymentGateway\Helper;

use Exception;
use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Undostres\PaymentGateway\Logger\Logger;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Framework\DB\Transaction;

/* HELPER CLASS, LOG, FRONT MESSAGE, SDK COMMUNICATION, ORDER VALIDATIONS */

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
    protected $messageManager;
    protected $logger;
    protected $session;
    protected $order;
    protected $storeManager;
    protected $gatewayConfig;
    protected $orderSender;
    protected $invoice;
    protected $transaction;

    /**
     * @param Config $gatewayConfig
     * @param Context $context
     * @param Logger $logger
     * @param Session $session
     * @param Order $order
     * @param StoreManagerInterface $storeManager
     * @param OrderSender $orderSender
     * @param InvoiceService $invoice
     * @param Transaction $transaction
     */
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
        SASDK::init($this->gatewayConfig->getKey() ?? "", $this->gatewayConfig->getUrl());
    }

    /**
     * LOG TO UDT FILE
     * @param string $message
     * @param int $type
     */
    public function log(string $message, int $type = self::LOG_DEBUG)
    {
        $message = "\n" . '========= UDT LOG =========' . "\n" . $message . "\n" . '========= UDT END =========  ==>  ';
        if ($this->gatewayConfig->canLog()) {
            if ($type === self::LOG_DEBUG) $this->logger->info($message);
            else if ($type === self::LOG_WARNING) $this->logger->warning($message);
            else if ($type === self::LOG_ERROR) $this->logger->critical($message);
        }
    }

    /**
     * ADD MESSAGE ON MAGENTO FRONTEND
     * @param $type
     * @param $msg
     * @return void
     */
    public function addFrontMessage($type, $msg)
    {
        if ($type === self::MSG_SUCCESS) $this->messageManager->addSuccess(__($msg));
        else if ($type === self::MSG_WARNING) $this->messageManager->addWarning(__($msg));
        else if ($type === self::MSG_ERROR) $this->messageManager->addError(__($msg));
    }

    /**
     * CHECK IF ORDER IS PENDING
     * @param $order
     * @return bool
     */
    public function isOrderPending($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_PENDING_PAYMENT;
    }

    /**
     * CHECK IF ORDER IS CANCELED
     * @param $order
     * @return bool
     */
    public function isOrderCanceled($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_CANCELED;
    }

    /**
     * CHECK IF ORDER IS PROCESSING
     * @param $order
     * @return bool
     */
    public function isOrderProcessing($order): bool
    {
        return $order !== null && $order->getState() === Order::STATE_PROCESSING;
    }

    /**
     * REDIRECT USING HEADER
     * @param $redirectUrl
     */
    public function redirectPage($redirectUrl)
    {
        header('Location: ' . $redirectUrl);
        die();
    }

    /**
     * RESPOND JSON USING HEADER (API)
     * @param $json
     */
    public function responseJSON($json)
    {
        echo json_encode($json);
        header("Content-Type: application/json; charset=utf-8");
        die();
    }

    /**
     * VALIDATE UDT HEADERS
     * @return bool
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

    /**
     * TRANSFORM ORDER TO JSON
     * @param $order
     * @return array
     * @throws Exception
     */
    public function getOrderJSON($order): array
    {
        $shippingAddress = $order->getShippingAddress();
        $shippingAddressParts = preg_split('/\r\n|\r|\n/', $shippingAddress->getData('street'));
        $orderId = $order->getRealOrderId();
        return [
            'currency' => $order->getOrderCurrencyCode(),
            'callbackUrl' => $this->getCallbackUrl(),
            'returnUrl' => $this->getReturnUrl($orderId),
            'reference' => (string)$orderId,
            'transactionId' => (string)$orderId,
            'paymentId' => (string)$orderId,
            'orderId' => (string)$orderId,
            'value' => $this->formatMoney($order->getTotalDue()),
            'installments' => 0,
            'paymentMethod' => "UnDosTres",
            'miniCart' => [
                "buyer" => [
                    "firstName" => $order->getCustomerFirstname(),
                    "lastName" => $order->getCustomerLastname(),
                    'email' => $order->getData('customer_email'),
                    'phone' => $order->getBillingAddress()->getData('telephone')
                ],
                "taxValue" => $this->formatMoney($order->getTaxAmount()),
                "shippingValue" => $this->formatMoney($order->getShippingAmount()),
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

    /**
     * TRANSFORM ORDER PRODUCTS TO JSON
     * @param $order
     * @return array
     * @throws Exception
     */
    public function getAllItems($order): array
    {
        $items = array();
        foreach ($order->getAllVisibleItems() as $item) {
            $items[] = array(
                'id' => (string)$item->getItemId(),
                'name' => $item->getName(),
                'price' => $this->formatMoney($item->getPrice()),
                'quantity' => (int)$item->getQtyOrdered(),
                'discount' => 0,
                'variation_id' => "0",
            );
        }
        return $items;
    }

    /**
     * GET API ROUTE TO CALLBACK
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'rest/V1/udt/callback';
    }

    /**
     * GET API ROUTE TO REDIRECT
     * @param $orderId
     * @return string
     */
    public function getReturnUrl($orderId): string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'rest/V1/udt/redirect?orderId=' . $orderId;
    }

    /**
     * MONEY FORMAT ####.##
     * @param $money
     * @return float
     */
    public function formatMoney($money): float
    {
        return SASDK::formatMoney($money);
    }

    /**
     * RESTORE CART
     * @return void
     */
    public function restoreCart()
    {
        $this->session->restoreQuote();
    }

    /**
     * CANCEL MAGENTO ORDER
     * @return void
     */
    public function cancelOrder($order)
    {
        if ($order !== null) $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED)->save();
    }

    /**
     * REDIRECT TO SUCCESS PAGE
     * @return void
     */
    public function redirectToCheckoutOnePageSuccess()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() . 'checkout/onepage/success');
    }

    /**
     * REDIRECT TO CART PAGE
     * @return void
     */
    public function redirectToCheckoutCart()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl() . 'checkout/cart');
    }

    /**
     * REDIRECT TO SHOP PAGE
     * @return void
     */
    public function redirectToShop()
    {
        $this->redirectPage($this->storeManager->getStore()->getBaseUrl());
    }

    /**
     * CHECK IF ORDER IT'S FROM UDT
     * @return bool
     */
    public function isUDTOrder($order): ?bool
    {
        if ($order === null) return null;
        return $order->getPayment()->getAdditionalInformation("method_title") === Config::CODE;
    }

    /**
     * RETURNS MAGENTO ORDER FROM SESSION OR ID
     * @return mixed
     */
    public function getOrder($orderId = null)
    {
        if ($orderId === null) $orderId = $this->session->getLastRealOrderId();
        if (!isset($orderId)) return null;
        $order = $this->order->loadByIncrementId($orderId);
        if (!$order->getId()) return null;
        return $order;
    }

    /**
     * CREATE ORDER PAYMENT WITH UDT ENDPOINT
     * @param $json
     * @return bool
     */
    public function createPayment($json): ?string
    {
        $response = SASDK::createPayment($json);
        if ($response["code"] !== 200) return null;
        return $response["response"];
    }

    /**
     * CANCEL ORDER WITH UDT ENDPOINT
     * @param $paymentId
     * @return bool
     */
    public function cancelUDTOrder($paymentId): bool
    {
        $response = SASDK::cancelOrder($paymentId);
        return $response["code"] !== 200;
    }

    /**
     * REFUND ORDER WITH UDT ENDPOINT
     * @param $paymentId
     * @param $transactionId
     * @param $value
     * @return bool
     */
    public function refundUDTOrder($paymentId, $transactionId, $value): bool
    {
        $response = SASDK::refundOrder($paymentId, $transactionId, $value);
        $this->log(sprintf("%s -> Envio refund: %s - %s - %s \nRecibio los datos:\n%s", __METHOD__, $paymentId, $transactionId, $value, json_encode($response)));
        return $response["code"] !== 200;
    }

    /**
     * PROCESS THE ORDER | CHANGES THE STATUS DEPENDING ON THE PARAMETER OF API
     * @param $paymentId
     * @param $status
     * @return array
     * @throws Exception
     */
    public function processOrder($paymentId, $status): array
    {
        $order = $this->getOrder($paymentId);
        if ($order === null) return ['code' => 404, 'message' => 'Orden no encontrada.'];
        if (!$this->isUDTOrder($order)) return ['code' => 500, 'message' => 'No es una orden de undostres.'];
        if (!$this->isOrderPending($order)) return ['code' => 500, 'message' => 'Estado previo inválido.'];
        switch ($status) {
            case 'approved':
                $this->invoiceOrder($order, $paymentId);
                $order->setState(Order::STATE_PROCESSING)->setStatus(Order::STATE_PROCESSING)->setIsCustomerNotified(true);
                $this->orderSender->send($order);
                $response = ['code' => 200, 'message' => 'Orden pagada correctamente.'];
                break;
            case 'denied':
                $order->setState(Order::STATE_CANCELED)->setStatus(Order::STATE_CANCELED);
                $response = ['code' => 200, 'message' => 'Orden cancelada correctamente.'];
                break;
            default:
                $response = ['code' => 500, 'message' => 'Nuevo estado incorrecto.'];
                break;
        }
        $response['paymentId'] = (string)$paymentId;
        $response['status'] = $order->getState();
        $order->save();
        return $response;
    }

    /**
     * GENERATE THE PAYMENT OF THE ORDER
     * @return void
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
