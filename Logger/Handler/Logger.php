<?php

namespace Undostres\PaymentGateway\Logger\Handler;

use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\Logger\Handler\Base;

class Logger extends Base
{
    protected $fileName = '/var/log/' . Config::CODE . ".log";
}
