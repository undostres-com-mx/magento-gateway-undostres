<?php

namespace Undostres\PaymentGateway\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Undostres\PaymentGateway\Helper\Helper;

/* EMPTY RESPONSE, THE REFUND IS MADE IN REFUND HANDLER */

class RefundRequest implements BuilderInterface
{
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function build(array $buildSubject)
    {
        return [];
    }
}
