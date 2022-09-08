<?php
namespace Undostres\PaymentGateway\Gateway\Handler;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Undostres\PaymentGateway\Model\Config;
use Undostres\PaymentGateway\Helper\Helper;

class VoidHandler implements HandlerInterface {
    protected $helper;

    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function handle($handlingSubject, $response) {
        $this->helper->log('In function ' . __FUNCTION__);
        if ($response['code'] !== 200)  throw new \Magento\Framework\Exception\CouldNotDeleteException(__("UnDosTres no se encuentra disponible."));
        $paymentDataObject = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDataObject->getPayment();
        $payment->setIsTransactionClosed(true);
        $payment->setShouldCloseParentTransaction(true);
        $this->helper->log('Finished');
    }
}
