<?php

namespace undostres\paymentGateway\Controller\Checkout;

use unDosTres\paymentGateway\Helper\AbstractAction;
use unDosTres\paymentGateway\Helper\Helper;

/* CONTROLLER WHERE PAYMENT URL IS CREATED AND REDIRECTION IS DONE */

class Index extends AbstractAction
{
    /* HANDLE ERROR AND REDIRECT */
    private function throwError($order, $restoreCart)
    {
        $this->addFrontMesage(Helper::MSG_WARNING, 'Su orden no fue procesada correctamente, favor de recargar la pagina');
        if ($restoreCart === true) $this->restoreCart();
        $this->cancelOrder($order);
        $this->redirectToCheckoutCart();
    }

    /* ACTION WHERE THE ORDER IS PASSED TO UDT GATEWAY AND REDIRECTION IS DONE */
    public function execute()
    {
        $order = $this->getOrder();
        try {
            if ($order === null) $this->throwError($order, false);
            else {
                if ($this->isOrderPending($order)) {
                    $json = $this->getOrderJSON($order);
                    $gatewayUrl = $this->getPaymentUrl($json);
                    if ($gatewayUrl === null) $this->throwError($order, true);
                    else $this->redirectPage($gatewayUrl);
                } else $this->throwError($order, false);
            }
        } catch (\Exception $ex) {
            $this->log('Ocurrió una excepción con la orden unDosTres/checkout/index: ' . $ex->getMessage());
            $this->log($ex->getTraceAsString());
            $this->throwError($order, true);
        }
    }
}
