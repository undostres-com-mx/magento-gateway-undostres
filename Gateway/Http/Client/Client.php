<?php

namespace Undostres\PaymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class Client implements ClientInterface
{
    /* IGNORED */
    public function placeRequest(TransferInterface $transferObject)
    {
        return ['IGNORED' => ['IGNORED']];
    }
}
