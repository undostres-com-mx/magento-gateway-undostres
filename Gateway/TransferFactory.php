<?php

namespace Undostres\PaymentGateway\Gateway;

use Magento\Payment\Gateway\Http\TransferFactoryInterface;
use Magento\Payment\Gateway\Http\TransferBuilder;

/* TRANSFER FACTORY IS WHERE API REQUEST ARE CREATED */

class TransferFactory implements TransferFactoryInterface
{
    private $transferBuilder;

    public function __construct(TransferBuilder $transferBuilder)
    {
        $this->transferBuilder = $transferBuilder;
    }

    public function create(array $request)
    {
        return $this->transferBuilder->setBody($request)->setMethod('POST')->build();
    }
}
