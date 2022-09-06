<?php

namespace Undostres\PaymentGateway\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MagentoLogger;

class Logger extends Base
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/my-sample-log.log';

    /**
     * @var
     */
    protected $loggerType = MagentoLogger::DEBUG;

}