<?php

namespace Undostres\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Undostres\PaymentGateway\Model\Config;

/**
 * First stage of voiding/cancelling an order. 
 * @package Undostres\PaymentGateway\Gateway\Request
 */
class VoidRequest implements BuilderInterface{

    public function build($buildSubject) {
        $toReturn = [];
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $order = $paymentDataObject->getOrder();
        if (
            $order->canCancel() &&
            $order->getPayment()->getMethod() === Config::CODE  // If method was UDT
        ) {
            $toReturn['orderId'] = $order->getRealOrderId();
        }

        return $toReturn;
    }
}
?>