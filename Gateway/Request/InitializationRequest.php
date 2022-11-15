<?php

namespace Undostres\PaymentGateway\Gateway\Request;

use Exception;
use Magento\Sales\Model\Order;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Undostres\PaymentGateway\Helper\Helper;
use Magento\Payment\Gateway\Helper\SubjectReader;

class InitializationRequest extends Helper implements BuilderInterface
{
    /**
     * BUILDS ENV REQUEST, GET THE PAYMENT AND THE ORDER, SET THE STATES AND NO MAIL SEND
     * @param array $buildSubject
     * @return string[][]
     */
    public function build(array $buildSubject)
    {
        try {
            $stateObject = $buildSubject['stateObject'];
            $paymentDO = SubjectReader::readPayment($buildSubject);
            $payment = $paymentDO->getPayment();
            $order = $payment->getOrder();
            $order->setCanSendNewEmailFlag(false);
            $stateObject->setState(Order::STATE_PENDING_PAYMENT);
            $stateObject->setStatus(Order::STATE_PENDING_PAYMENT);
            $stateObject->setIsNotified(false);
        } catch (Exception $e) {
            $this->log(sprintf("%s -> Exception: %s", __METHOD__, $e->getMessage()), Helper::LOG_ERROR);
        }
        return ['IGNORED' => ['IGNORED']];
    }
}
