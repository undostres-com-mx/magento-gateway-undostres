<?php

namespace Undostres\PaymentGateway\Observer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;
use Undostres\PaymentGateway\Model\Config;

/* GATEWAYS DISABLER ON COOKIE */

class DisableGateways implements ObserverInterface
{
    protected $cookieManager;
    /** @var Config */
    private $config;

    public function __construct(Config $config, CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        $xd = $this->config->isActive();
        if ($xd) {
            $result = $observer->getEvent()->getResult();
            $method_instance = $observer->getEvent()->getMethodInstance();
            $cookieValue = $this->cookieManager->getCookie('UDT');
            if (($cookieValue == "isUDT" && $method_instance->getCode() != 'Undostres_Gateway') || ($cookieValue != "isUDT" && $method_instance->getCode() == 'Undostres_Gateway'))
                $result->setData('is_available', false);
        }
    }
}
