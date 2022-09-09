<?php

namespace Undostres\PaymentGateway\Gateway;

use Magento\Framework\Exception\CouldNotDeleteException;
use Undostres\PaymentGateway\Helper\Helper;
use Undostres\PaymentGateway\Model\Config;

class Cancel extends Helper
{
    /**
     * ADMIN CANCEL INTERCEPTOR | CANCEL UDT ORDER
     * @param $subject
     */
    public function beforeCancel($subject)
    {
        if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) {
            if($this->cancelUDTOrder($subject->getRealOrderId()))
                throw new CouldNotDeleteException("UnDosTres no se encuentra disponible.");
        }
    }
}
