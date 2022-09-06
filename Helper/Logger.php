<?php

namespace Undostres\PaymentGateway\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Logger\Monolog;
use Monolog\Handler\StreamHandler;
use Undostres\PaymentGateway\Model\Config;


class Logger extends Monolog
{
    /**
     * @var string
     */
    protected $fileName = '/var/log/my-sample-log.log';

    /**
     * @var
     */
    protected $loggerType = Logger::DEBUG;

    const INFO = 100;
    protected $gatewayConfig;

    /**
     * Logger constructor.
     * @param Config $gatewayConfig
     * @param DirectoryList $directoryList
     * @param string $name
     * @param array $handlers
     */
    public function __construct(Config $gatewayConfig,DirectoryList $directoryList, string $name, array $handlers = [])
    {
        $this->gatewayConfig = $gatewayConfig;
        try {
            $handlers[] = new StreamHandler($directoryList->getPath('log') . Config::CODE . ".log", self::INFO);
        } catch (FileSystemException|Exception $e) {
        }
        parent::__construct($name, $handlers, []);
    }

    /**
     * @param int $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        $message  = "\n" . '========= UDT LOG =========' . "\n" . $message . "\n" .  '========= UDT END =========' . "\n\n";
        if ($this->gatewayConfig->canLog()) parent::addRecord(1, $message, $context);
    }
}
