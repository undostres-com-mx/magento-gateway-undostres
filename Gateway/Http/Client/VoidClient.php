<?php
namespace Undostres\PaymentGateway\Gateway\Http\Client;

use UDT\SDK\SASDK;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Undostres\PaymentGateway\Model\Config;

class VoidClient implements ClientInterface
{
    public function placeRequest(TransferInterface $transferObject)
    {
        $body = $transferObject->getBody();
        if (isset($body['orderId'])) return [];
        $orderId = $body['orderId'];
        $response = SASDK::cancelOrder($orderId);

        /*if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) { // CHECK IF WE CAN CANCEL AND IS UDT PAYMENT
            $response = SASDK::cancelOrder((string)$subject->getRealOrderId());
            if ($response['code'] !== 200)  throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
        }*/


        return $response;
    }
}



?>