<?php

namespace Undostres\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Undostres\PaymentGateway\Model\Config;
use Undostres\PaymentGateway\Helper\Helper;

/**
 * First stage of voiding/cancelling an order. 
 * @package Undostres\PaymentGateway\Gateway\Request
 */
class VoidRequest implements BuilderInterface{
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function build($buildSubject) {
        $this->helper->log('In function ' . __FUNCTION__);
        $toReturn = [];
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();
        $this->helper->log(sprintf('Can cancel: %s', [$order->canCancel()]));
        $this->helper->log(sprintf('Method: %s', [$order->getPayment()->getMethod()]));
        if (
            $order->canCancel() &&
            $order->getPayment()->getMethod() === Config::CODE  // If method was UDT
        ) {
            $toReturn['orderId'] = $order->getRealOrderId();
        }

        $this->helper->log(sprintf('To return: %s', [json_encode($toReturn)]));

        return $toReturn;
    }
}
?>