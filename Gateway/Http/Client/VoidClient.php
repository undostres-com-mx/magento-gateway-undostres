<?php
namespace Undostres\PaymentGateway\Gateway\Http\Client;

use UDT\SDK\SASDK;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;

class VoidClient implements ClientInterface
{
    public function placeRequest(TransferInterface $transferObject)
    {
        $body = $transferObject->getBody();
        if (isset($body['orderId'])) return [];
        $orderId = $body['orderId'];
        $response = SASDK::cancelOrder($orderId);

        return $response;
    }
}



?>