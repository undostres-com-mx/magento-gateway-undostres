<?php

namespace Undostres\PaymentGateway\Gateway\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Undostres\PaymentGateway\Helper\Helper;
use Exception;

class RefundHandler extends Helper implements HandlerInterface
{
    /**
     * VALIDATE IF ORDER CAN BE REFUNDED AND REFUNDS THROUGH UDT
     * @param array $handlingSubject
     * @param array $response
     * @return void
     * @throws Exception
     */
	public function handle(array $handlingSubject, array $response)
	{
		$refund_amount = $handlingSubject['amount'];
		$payment = $handlingSubject['payment']->getPayment();
		$transaction_id = $payment->getData()['creditmemo']->getData('invoice')->getData('transaction_id');
        if (empty($payment) || empty($payment->getData('creditmemo')))
            throw new Exception('No podemos realizar un reembolso porque no hay una transacción de captura.');
        if($this->refundUDTOrder($transaction_id, $transaction_id, $this->formatMoney($refund_amount)))
            throw new Exception("UnDosTres no se encuentra disponible.");
	}
}
