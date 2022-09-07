<?php

namespace Undostres\PaymentGateway\Api;

use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Helper\Helper;
use Undostres\PaymentGateway\Model\Config;

/* API CLASS TO MANAGE REDIRECT, CALLBACK AND STATUS */
class Api extends Helper
{

    public function callback($paymentId, $status)
    {
        $this->log("xd");
        $this->log("xd",self::LOG_WARNING);
        $this->log("xd",self::LOG_ERROR);
        return $paymentId;
       /* $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.0';

        try{
            $validate = false;

            $Headers = $this->getHeaders();
            if(isset($Headers['x-vtex-api-appkey'], $Headers['x-vtex-api-apptoken'])) {
                $validate = $this->isAuthenticRequest($Headers['x-vtex-api-appkey'], $Headers['x-vtex-api-apptoken']);
            }

            if($validate){
                $Body = $this->getBody();
                $orderId = $Body['paymentId'];
                $status = $Body['status'];

                $order = $this->_objectManager->create('\Magento\Sales\Model\Order')->loadByIncrementId($orderId);
                if($order->getStatus() !== 'pending_payment'){
                    $msg = 'The current status order is not pending_payment';
                    echo json_encode($response = ['success' => false, 'code' => 400, 'msg' => $msg]);
                    header($protocol.' '.$response['code'].' '.$msg);
                    die( );
                }
                $this->_logger->info("updating status order: ".$orderId.", to ".$status);
                switch($status) {
                    case 'approved':
                        $this->invoiceOrder($order, $orderId);

                        $orderState = Order::STATE_PROCESSING; // complete means the order has been shipped already
                        $order->setState($orderState)          // processing means payment is done but the order needs shipment
                        ->setStatus($orderState)
                            ->setIsCustomerNotified(true);
                        $order->save();
                        $emailSender = $this->_objectManager->create('\Magento\Sales\Model\Order\Email\Sender\OrderSender');
                        $emailSender->send($order);

                        $msg = 'status order changed successfully to '.$orderState;
                        $this->_logger->info($msg);
                        echo json_encode($response = ['success' => true, 'code' => 200, 'msg' => $msg]);
                        header($protocol.' '.$response['code'].' '.$msg);
                        die( );
                    case 'denied':
                        $orderState = Order::STATE_CANCELED;
                        $order->setState($orderState)->setStatus($orderState);
                        $order->save();

                        $msg = 'status order changed successfully to '.$orderState;
                        $this->_logger->info($msg);
                        echo json_encode($response = ['success' => true, 'code' => 200, 'msg' => $msg]);
                        header($protocol.' '.$response['code'].' '.$msg);
                        die( );

                    default:
                        $msg = 'unknown status order';
                        $this->_logger->info($msg);
                        echo json_encode($response = ['success' => false, 'code' => 400, 'msg' => $msg]);
                        header($protocol.' '.$response['code'].' '.$msg);
                        die( );
                }
            } else {
                $msg = 'Unauthorized request';
                echo json_encode($response = ['success' => false, 'code' => 401, 'msg' => $msg]);
                header($protocol.' '.$response['code'].' '.$msg);
                die( );
            }
        } catch (\Exception $e) {
            $msg = 'Internal Server Error';
            $this->_logger->info($msg.', Exception'.$e->getMessage());
            echo json_encode($response = ['success' => false, 'code' => 500, 'msg' => $msg]);
            header($protocol.' '.$response['code'].' '.$msg);
            die( );
        }*/
    }

    public function redirect($orderId)
    {
        $order = $this->getOrder();
        if ($this->isOrderProcessing($order)) {
            $this->addFrontMesage(Helper::MSG_SUCCESS, '¡Felicidades!, tu pago con UnDosTres fue exitoso.');
            $this->redirectToCheckoutOnePageSuccess();
        } else if ($this->isOrderCanceled($order)) {
            $this->addFrontMesage(Helper::MSG_SUCCESS, 'Tu pago con UnDosTres fue cancelado.');
            $this->restoreCart();
            $this->redirectToCheckoutCart();
        } else {
            $this->addFrontMesage(Helper::MSG_ERROR, 'Orden invalida.');
            $this->redirectToCheckoutCart();
        }
    }

    private function getHeaders() {
        $serverHeaders = apache_request_headers();
        $Headers = array();
        foreach ($serverHeaders as $header => $value) {
            $Headers[strtolower($header)] = $value;
        }
        return $Headers;
    }

    private function getBody() {
        $requestBody = file_get_contents('php://input');
        $Body = json_decode($requestBody, true);
        return $Body;
    }

    private function invoiceOrder($order, $transactionId){
        if(!$order->canInvoice()){
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Cannot create an invoice.')
            );
        }

        $invoice = $this->_objectManager->create('Magento\Sales\Model\Service\InvoiceService')
            ->prepareInvoice($order);

        if (!$invoice->getTotalQty()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create an invoice without products.')
            );
        }

        $invoice->setTransactionId($transactionId);
        $invoice->setRequestedCaptureCase(Order\Invoice::CAPTURE_ONLINE);
        $invoice->setState(Order\Invoice::STATE_PAID);
        $invoice->register();
        $invoice->pay();

        $transaction = $this->_objectManager->create('Magento\Framework\DB\Transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        $transaction->save();
    }
}
