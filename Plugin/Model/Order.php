<?php

namespace unDosTres\paymentGateway\Plugin\Model;

use unDosTres\paymentGateway\Helper\Helper;
use unDosTres\paymentGateway\Gateway\Config\Config;

/* ORDER CANCEL ON ADMIN PANEL */

class Order extends Helper
{
    public function beforeCancel($subject)
    {
        if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) { // CHECK IF CAN CANCEL AND IS UDT PAYMENT
            $json = array(
                'paymentId'     => (string)$subject->getRealOrderId(),
                'transactionId' => (string)$subject->getRealOrderId(),
                'requestId'     => (string)$subject->getRealOrderId(), // overwritted by SDK
            );
            if(!$this->callUdtSdk(Helper::CANCEL, $json)){
                throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
            }
        }
    }
}
