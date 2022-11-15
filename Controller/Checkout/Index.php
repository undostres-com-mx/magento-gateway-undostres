<?php

namespace Undostres\PaymentGateway\Controller\Checkout;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Undostres\PaymentGateway\Helper\Helper;

/* ACTION WHERE THE ORDER IS PASSED TO UDT GATEWAY AND REDIRECTION IS DONE */

class Index extends Action
{
    protected $helper;

    /**
     * @param Context $context
     * @param Helper $helper
     */
    public function __construct(Context $context, Helper $helper)
    {
        parent::__construct($context);
        $this->helper = $helper;
    }

    public function execute()
    {
        $order = $this->helper->getOrder();
        try {
            if ($order === null) throw new Exception("Orden no encontrada.");
            else {
                if ($this->helper->isOrderPending($order)) {
                    $json = $this->helper->getOrderJSON($order);
                    $gatewayUrl = $this->helper->createPayment($json);
                    $this->helper->log(sprintf("%s -> Payment url request send: %s \nReceive:\n%s", __METHOD__, json_encode($json), json_encode($gatewayUrl)));
                    if ($gatewayUrl === null) throw new Exception("Error al redireccionar a UnDosTres, por favor intentalo más tarde.");
                    else $this->helper->redirectPage($gatewayUrl);
                } else throw new Exception("El estatus de la orden es incorrecto.");
            }
        } catch (Exception $e) {
            $this->helper->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
            $this->helper->addFrontMessage(Helper::MSG_WARNING, $e->getMessage());
            if ($order !== null) {
                $this->helper->restoreCart();
                $this->helper->cancelOrder($order);
            }
            $this->helper->redirectToCheckoutCart();
        }
    }
}
