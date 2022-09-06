<?php

namespace Undostres\PaymentGateway\Helper;

use UDT\SDK\SASDK;
use Undostres\PaymentGateway\Model\Config;
use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Checkout\Model\Session;
use Magento\Sales\Model\Order;
use Magento\Framework\Logger\Monolog;
use Undostres\PaymentGateway\Logger\Logger;

/* PHP CLASS WHIT COMMON FUNCTIONS */

class Helper
{
    const LOG_DEBUG = 0;
    const LOG_WARNING = 1;
    const LOG_ERROR = 2;

    protected $logger;
    protected $orderFactory;
    protected $session;
    protected $messager;
    protected $storeManager;
    protected $gatewayConfig;

    public function __construct(Config $gatewayConfig, Context $context, Logger $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->messager = $context->getMessageManager();
        $this->logger = $logger;
        $this->session = $session;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
        $this->gatewayConfig = $gatewayConfig;
        SASDK::init($this->gatewayConfig->getKey(), $this->gatewayConfig->getUrl());
    }

    public function log(string $message, int $type = self::LOG_DEBUG)
    {
        $message = "\n" . '========= UDT LOG =========' . "\n" . $message . "\n" . '========= UDT END =========' . "\n\n";
        if ($this->gatewayConfig->canLog()) {
            if($type=== self::LOG_DEBUG) $this->logger->info($message);
            else if($type=== self::LOG_WARNING) $this->logger->warning($message);
            else if($type=== self::LOG_ERROR) $this->logger->critical($message);
        }
    }

}
