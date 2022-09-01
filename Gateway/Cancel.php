<?php

namespace unDosTres\paymentGateway\Gateway;

use UDT\SDK\SASDK;
use unDosTres\paymentGateway\Helper\Helper;
use unDosTres\paymentGateway\Gateway\Config\Config;

/* ORDER CANCEL ON ADMIN PANEL */
class Cancel extends Helper2
{
    public function beforeCancel($subject)
    {
        if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) { // CHECK IF WE CAN CANCEL AND IS UDT PAYMENT
            $response = SASDK::cancelOrder((string)$subject->getRealOrderId());
            if ($response['code'] !== 200)  throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
        }
    }
}
