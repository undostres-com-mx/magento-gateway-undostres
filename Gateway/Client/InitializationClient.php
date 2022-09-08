<?php

namespace Undostres\PaymentGateway\Gateway\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class InitializationClient implements ClientInterface
{
    /* IGNORED */
    public function placeRequest(TransferInterface $transferObject)
    {
        return ['IGNORED' => ['IGNORED']];
    }
}
