<?php

namespace unDosTres\paymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/* EMPTY RESPONSE, THE REFUND IS MADE INT MODEL/CONFIGPROVIDER.PHP */

class RefundRequest implements BuilderInterface
{
    public function build(array $buildSubject)
    {
        return [];
    }
}
