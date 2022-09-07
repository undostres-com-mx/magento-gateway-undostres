<?php
namespace Undostres\PaymentGateway\Gateway\Response;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Undostres\PaymentGateway\Model\Config;

class VoidHandler implements HandlerInterface {
    public function handle($handlingSubject, $response) {
        if ($response['code'] !== 200)  throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDataObject->getPayment();
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(true);
    }
}

?>