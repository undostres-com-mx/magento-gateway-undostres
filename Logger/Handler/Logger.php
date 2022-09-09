<?php

namespace Undostres\PaymentGateway\Logger\Handler;

use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\Logger\Handler\Base;

/* NAME OF LOG FILE */

class Logger extends Base
{
    protected $fileName = '/var/log/' . Config::CODE . ".log";
}
