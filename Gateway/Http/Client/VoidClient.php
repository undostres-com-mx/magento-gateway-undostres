<?php
namespace Undostres\PaymentGateway\Gateway\Http\Client;

use UDT\SDK\SASDK;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Undostres\PaymentGateway\Model\Config;
use Undostres\PaymentGateway\Helper\Helper;

class VoidClient implements ClientInterface
{
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function placeRequest(TransferInterface $transferObject)
    {
        $this->helper->log('In function ' . __FUNCTION__);
        $body = $transferObject->getBody();
        if (isset($body['orderId'])) return [];
        $orderId = $body['orderId'];
        $this->helper->log(sprintf('About to cancel order id %d', [$orderId]));
        $response = SASDK::cancelOrder($orderId);
        $this->helper->log(sprintf('SDK\'s response : %s', [json_encode($response)]));

        /*if ($subject->canCancel() && $subject->getPayment()->getMethod() === Config::CODE) { // CHECK IF WE CAN CANCEL AND IS UDT PAYMENT
            $response = SASDK::cancelOrder((string)$subject->getRealOrderId());
            if ($response['code'] !== 200)  throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
        }*/


        return $response;
    }
}



?>