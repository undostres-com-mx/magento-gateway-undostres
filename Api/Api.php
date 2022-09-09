<?php

namespace Undostres\PaymentGateway\Api;

use Exception;
use Undostres\PaymentGateway\Helper\Helper;

/* API CLASS TO MANAGE REDIRECT, CALLBACK AND STATUS */

class Api extends Helper
{
    /**
     * CALLBACK OF UDT | HERE IS THE LOGIC TO CHANGE THE STATUS OF ORDERS
     * @return void
     */
    public function callback($paymentId, $status)
    {
        try {
            if (!$this->areValidHeaders()) throw new Exception("Headers invalidas.");
            $this->log(sprintf("%s -> Callback de la orden: %s con el estatus: %s", __METHOD__, $paymentId, $status));
            $response = $this->processOrder($paymentId, $status);
            $this->log(sprintf("%s -> Callback correcto de la orden: %s", __METHOD__, $paymentId));
            $this->responseJSON($response);
        } catch (Exception $e) {
            $this->log('Exception' . $e->getMessage(), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * REDIRECT OF UDT TO STORE | HERE IS THE LOGIC TO WHERE TO REDIRECT
     * @return void
     */
    public function redirect($orderId)
    {
        $order = $this->getOrder($orderId);
        $this->log(sprintf("%s -> Redirect de la orden: %s", __METHOD__, $orderId));
        if ($this->isOrderProcessing($order)) {
            $this->addFrontMessage(Helper::MSG_SUCCESS, '¡Felicidades!, tu pago con UnDosTres fue exitoso.');
            $this->redirectToCheckoutOnePageSuccess();
        } else if ($this->isOrderCanceled($order)) {
            $this->addFrontMessage(Helper::MSG_WARNING, 'Tu pago con UnDosTres fue cancelado.');
            $this->restoreCart();
        } else $this->addFrontMessage(Helper::MSG_ERROR, 'Orden invalida.');
        $this->redirectToCheckoutCart();
    }
}
