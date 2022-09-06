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

/* PHP CLASS WHIT COMMON FUNCTIONS */

class Helper
{
    protected $logger;
    protected $orderFactory;
    protected $session;
    protected $messager;
    protected $storeManager;
    protected $gatewayConfig;

    public function __construct(Config $gatewayConfig, Context $context, LoggerInterface $logger, Session $session, OrderFactory $orderFactory, StoreManagerInterface $storeManager)
    {
        $this->messager = $context->getMessageManager();
        $this->logger = $logger;
        $this->session = $session;
        $this->orderFactory = $orderFactory;
        $this->storeManager = $storeManager;
        $this->gatewayConfig = $gatewayConfig;
        SASDK::init($this->gatewayConfig->getKey(), $this->gatewayConfig->getUrl());
    }

}
