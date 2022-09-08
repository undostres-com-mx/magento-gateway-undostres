<?php
namespace Undostres\PaymentGateway\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;

class GeneralResponseValidator extends AbstractValidator {
    public function validate($validationSubject) {
        return $this->createResult(true, [], []);
    }
}
?>