<?php

namespace Undostres\PaymentGateway\Gateway;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Phrase;
use Undostres\PaymentGateway\Helper\Helper;

class Cancel extends Helper
{
    /**
     * ADMIN CANCEL INTERCEPTOR | CANCEL UDT ORDER
     * @param $order
     */
    public function beforeCancel($order)
    {
        if ($order->canCancel() && $this->isUDTOrder($order))
            if ($this->cancelUDTOrder($order->getRealOrderId()))
                throw new CouldNotDeleteException(new Phrase("UnDosTres no se encuentra disponible."));
    }
}
