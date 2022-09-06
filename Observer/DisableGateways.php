<?php

namespace Undostres\PaymentGateway\Observer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Undostres\PaymentGateway\Model\Config;
use Undostres\PaymentGateway\Logger\Logger;

/* GATEWAYS DISABLER ON COOKIE */

class DisableGateways implements ObserverInterface
{
    protected $gatewayConfig;
    protected $cookieManager;
    protected $logger;

    public function __construct( Logger $logger, Config $gatewayConfig, CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
        $this->gatewayConfig = $gatewayConfig;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $this->logger->info("XD");
        if ($this->gatewayConfig->isActive()) {
            $result = $observer->getEvent()->getResult();
            $method_instance = $observer->getEvent()->getMethodInstance();
            $cookieValue = $this->cookieManager->getCookie('UDT');
            if (($cookieValue == "isUDT" && $method_instance->getCode() != 'Undostres_Gateway') || ($cookieValue != "isUDT" && $method_instance->getCode() == 'Undostres_Gateway'))
                $result->setData('is_available', false);
        }
    }
}
