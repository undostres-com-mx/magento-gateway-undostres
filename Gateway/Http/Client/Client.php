<?php

namespace Undostres\paymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class Client implements ClientInterface
{
    /* IGNORED */
    public function placeRequest(TransferInterface $transferObject)
    {
        $response = ['IGNORED' => ['IGNORED']];
        return $response;
    }
}
