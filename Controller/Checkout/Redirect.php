<?php

namespace Undostres\PaymentGateway\Controller\Checkout;

use Undostres\PaymentGateway\Helper\AbstractAction;
use Undostres\PaymentGateway\Helper\Helper;

/* CONTROLLER WHERE PAYMENT IS VALIDATED AND ORDER ACCEPTED */

class Redirect extends AbstractAction
{
    /* ACTION WHEN REDIRECTION OF UDT PAYMENT IS DONE */
    public function execute()
    {
        $order = $this->getOrder();
        if ($this->isOrderProcesing($order)) {
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
}
