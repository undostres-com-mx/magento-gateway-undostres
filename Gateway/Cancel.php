<?php

namespace Undostres\PaymentGateway\Gateway;

use Magento\Framework\Exception\CouldNotDeleteException;
use Undostres\PaymentGateway\Helper\Helper;
use Undostres\PaymentGateway\Model\Config;

/* ORDER CANCEL ON ADMIN PANEL */

class Cancel extends Helper
{
    public function beforeCancel($subject)
    {
        if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) {
            if($this->cancelUDTOrder($subject->getRealOrderId()))
                throw new CouldNotDeleteException("UnDosTres no se encuentra disponible.");
        }
    }
}
