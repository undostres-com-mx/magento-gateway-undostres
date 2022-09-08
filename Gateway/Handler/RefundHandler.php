<?php

namespace Undostres\PaymentGateway\Gateway\Handler;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Undostres\PaymentGateway\Helper\Helper;
use Magento\Framework\Exception\LocalizedException;

class RefundHandler extends Helper implements HandlerInterface
{
	public function handle(array $handlingSubject, array $response)
	{
		$refund_amount = $handlingSubject['amount'];
		$payment = $handlingSubject['payment']->getPayment();
		$transaction_id = $payment->getData()['creditmemo']->getData('invoice')->getData('transaction_id');
        if (empty($payment) || empty($payment->getData('creditmemo')))
            throw new LocalizedException(__('No podemos realizar un reembolso porque no hay una transacción de captura.'));
        if($this->refundUDTOrder($transaction_id, $transaction_id, $this->moneyFormat($refund_amount)))
            throw new LocalizedException("UnDosTres no se encuentra disponible.");
	}
}
