<?php

namespace Undostres\PaymentGateway\Gateway\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

/* IGNORED */

class InitializationClient implements ClientInterface
{
    public function placeRequest(TransferInterface $transferObject)
    {
        return ['IGNORED' => ['IGNORED']];
    }
}
