<?php

namespace Undostres\PaymentGateway\Api;

use Exception;
use Undostres\PaymentGateway\Helper\Helper;

/* API CLASS TO MANAGE REDIRECT, CALLBACK AND STATUS */

class Api extends Helper
{
    public function callback($paymentId, $status)
    {
        try {
            if (!$this->areValidHeaders()) throw new Exception("Headers invalidas.");
            $this->log(sprintf("%s | %s -> Callback de la orden: %s con el estatus: %s", __CLASS__, __METHOD__, $paymentId, $status));
            $response = $this->processOrder($paymentId, $status);
            $this->responseJSON($response, $response["code"], $response["message"]);
        } catch (Exception $e) {
            $this->log('Exception' . $e->getMessage(), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()], 500, "Internal Server Error");
        }
    }

    public function redirect($orderId)
    {
        $order = $this->getOrder($orderId);
        if ($this->isOrderProcessing($order)) {
            $this->addFrontMesage(Helper::MSG_SUCCESS, '¡Felicidades!, tu pago con UnDosTres fue exitoso.');
            $this->redirectToCheckoutOnePageSuccess();
        } else if ($this->isOrderCanceled($order)) {
            $this->addFrontMesage(Helper::MSG_WARNING, 'Tu pago con UnDosTres fue cancelado.');
            $this->restoreCart();
        } else $this->addFrontMesage(Helper::MSG_ERROR, 'Orden invalida.');
        $this->redirectToCheckoutCart();
    }
}
