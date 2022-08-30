<?php

namespace unDosTres\paymentGateway\Gateway\Http\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class RefundClient implements ClientInterface
{
    /* REFUND REQUEST */
    public function placeRequest(TransferInterface $transferObject)
    {
        $response = $transferObject->getBody();
        return $response;
    }
}
