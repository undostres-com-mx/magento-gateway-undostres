<?php

namespace unDosTres\paymentGateway\Model;

use Magento\Payment\Gateway\Response\HandlerInterface;
use unDosTres\paymentGateway\PrivateConfig;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Model\Method\Logger;
use UDT\SDK\SDK;
use unDosTres\paymentGateway\Gateway\Config\Config;
use Magento\Payment\Helper\Data;

class UnDosTresPayment extends \Magento\Payment\Model\Method\AbstractMethod implements HandlerInterface
{
	public $_isGateway = true;
	public $_canRefund = true;
	public $_canRefundInvoicePartial = true;
	public $_canCapture = true;
	public $_canCapturePartial = true;
	public $_scopeConfig;

	public function __construct(
		Context $context,
		Registry $registry,
		ExtensionAttributesFactory $extensionFactory,
		AttributeValueFactory $customAttributeFactory,
        Data $paymentData,
		ScopeConfigInterface $scopeConfig,
		Logger $logger
	) {
		parent::__construct(
			$context,
			$registry,
			$extensionFactory,
			$customAttributeFactory,
            $paymentData,
			$scopeConfig,
			$logger
		);
		$this->_scopeConfig = $scopeConfig;
	}

    private function makeRefund($payload){
        $request['refund'] = $payload;
		$sdk = new SDK(PrivateConfig::SDK);
        $response = $sdk->handlePayload(json_encode($request));
        $response["code"] = isset($response["code"]) ? $response["code"] : 500;

		$this->_logger->info(__('Request refund: '.json_encode($request)));
		$this->_logger->info(__('Response refund: '.json_encode($response)));


        return $response;
    }
	public function handle(array $handlingSubject, array $response)
	{
		$refund_amount = $handlingSubject['amount'];
		$payment = $handlingSubject['payment']->getPayment();
		$transaction_id = $payment->getData()['creditmemo']->getData('invoice')->getData('transaction_id');

		$request = [
			'value' => floatval(round($refund_amount, 2)),
			'transactionId' => ''.$transaction_id,
			'paymentId' => ''.$transaction_id,
			'requestId' => ''.$transaction_id // this is overwrited by the SDK
		];
		if (empty($payment) || empty($payment->getData('creditmemo'))) {
			throw new LocalizedException(
				__('No podemos realizar un reembolso porque no hay una transacción de captura.')
			);
		}
		$response = $this->makeRefund($request);

		if($response['code'] === 200){
				$this->_logger->info('Success in refund'.json_encode($response));
				return $this;
		}else{
				$this->_logger->error(__(json_encode($response)));
				$error_message = "error, por favor revisa el código de error: "."code: ".$response['code'];
				throw new LocalizedException(__($error_message));
		}
	}
}
