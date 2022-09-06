<?php

namespace Undostres\PaymentGateway\Controller\Checkout;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Undostres\PaymentGateway\Helper\Helper;

/* ACTION WHERE THE ORDER IS PASSED TO UDT GATEWAY AND REDIRECTION IS DONE */

class Index extends Action
{
    protected $helper;

    public function __construct(Context $context, Helper $helper)
    {
        parent::__construct(context);
        $this->helper = $helper;
    }

    /* HANDLE ERROR AND REDIRECT */
    private function throwError($order, $restoreCart)
    {
        /* $this->addFrontMesage(Helper::MSG_WARNING, 'Su orden no fue procesada correctamente, favor de recargar la pagina');
         if ($restoreCart === true) $this->restoreCart();
         $this->cancelOrder($order);
         $this->redirectToCheckoutCart();*/
    }


    public function execute()
    {
        $this->helper->log("Entro al index :D ");
        //$order = $this->getOrder();
        /* try {
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
             $this->log('Ocurrió una excepción con la orden Undostres/checkout/index: ' . $ex->getMessage());
             $this->log($ex->getTraceAsString());
             $this->throwError($order, true);
         }*/
    }
}
