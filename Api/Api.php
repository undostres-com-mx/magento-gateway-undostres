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
            $this->responseJSON($response);
        } catch (Exception $e) {
            $this->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * REDIRECT OF UDT TO STORE | HERE IS THE LOGIC TO WHERE TO REDIRECT
     * @return void
     */
    public function redirect($orderId)
    {
        $this->log(sprintf("%s -> Redirect de la orden: %s", __METHOD__, $orderId));
        $order = $this->getOrder($orderId);
        if ($order !== null & $this->isUDTOrder($order)) {
            if ($this->isOrderProcessing($order)) {
                $this->addFrontMessage(Helper::MSG_SUCCESS, '¬°Felicidades!, tu pago con UnDosTres fue exitoso.');
                $this->redirectToCheckoutOnePageSuccess();
            } else if ($this->isOrderCanceled($order)) {
                $this->addFrontMessage(Helper::MSG_WARNING, 'Tu pago con UnDosTres fue cancelado.');
                $this->restoreCart();
                $this->redirectToCheckoutCart();
            }
        }
        $this->redirectToShop();
    }

    /**
     * PARSE THE STATUS TO UDT STANDARD STATUSES
     * @return void
     */
    public function status($orderId)
    {
        try {
            if (!$this->areValidHeaders()) throw new Exception("Headers invalidas.");
            $order = $this->getOrder($orderId);
            $this->log(sprintf("%s -> Consulta estatus de la orden: %s", __METHOD__, $orderId));
            if ($order === null) $response = ['code' => 404, 'message' => 'Orden no encontrada.'];
            else if (!$this->isUDTOrder($order)) $response = ['code' => 500, 'message' => 'Orden no creada por UnDosTres.'];
            else $response = ['code' => 200, 'message' => 'Ok.', 'status' => $order->getState()];
            $this->responseJSON($response);
        } catch (Exception $e) {
            $this->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * GET LOG FILE AND RETURNS IT
     * @return void
     */
    public function getLogs()
    {
        try {
            if (!$this->areValidHeaders()) throw new Exception("Headers invalidas.");
            $this->responseJSON(['success' => true, 'code' => 200, 'msg' => $this->getLogsToText()]);
        } catch (Exception $e) {
            $this->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * GET LOG FILE AND RETURNS IT
     * @return void
     */
    public function deleteLogs()
    {
        try {
            if (!$this->areValidHeaders()) throw new Exception("Headers invalidas.");
            $this->deleteAllLogs();
            $this->responseJSON(['success' => true, 'code' => 200]);
        } catch (Exception $e) {
            $this->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
            $this->responseJSON(['success' => false, 'code' => 500, 'msg' => $e->getMessage()]);
        }
    }
}
