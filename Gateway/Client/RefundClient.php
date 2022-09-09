<?php

namespace Undostres\PaymentGateway\Gateway\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

/* REFUND REQUEST */

class RefundClient implements ClientInterface
{
    public function placeRequest(TransferInterface $transferObject)
    {
        return $transferObject->getBody();
    }
}
