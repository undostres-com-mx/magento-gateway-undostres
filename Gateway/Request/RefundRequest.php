<?php

namespace Undostres\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/* EMPTY RESPONSE | THE REFUND IS MADE IN REFUND HANDLER */

class RefundRequest implements BuilderInterface
{
    public function build(array $buildSubject)
    {
        return [];
    }
}
