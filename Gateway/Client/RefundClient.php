<?php

namespace Undostres\PaymentGateway\Gateway\Client;

use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class RefundClient implements ClientInterface
{
    /* REFUND REQUEST */
    public function placeRequest(TransferInterface $transferObject)
    {
        return $transferObject->getBody();
    }
}
