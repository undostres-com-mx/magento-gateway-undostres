<?php

namespace Undostres\PaymentGateway\Observer;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Undostres\PaymentGateway\Model\Config;

/* DISABLE THE PAYMENT GATEWAYS BASED ON COOKIE VALUE */

class DisableGateways implements ObserverInterface
{
    protected $gatewayConfig;
    protected $cookieManager;

    /**
     * @param Config $gatewayConfig
     * @param CookieManagerInterface $cookieManager
     */
    public function __construct(Config $gatewayConfig, CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
        $this->gatewayConfig = $gatewayConfig;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->gatewayConfig->isActive()) {
            $result = $observer->getEvent()->getResult();
            $method_instance = $observer->getEvent()->getMethodInstance();
            $cookieValue = $this->cookieManager->getCookie('UDT');
            if (($cookieValue == "isUDT" && $method_instance->getCode() != 'Undostres_Gateway') || ($cookieValue != "isUDT" && $method_instance->getCode() == 'Undostres_Gateway'))
                $result->setData('is_available', false);
        }
    }
}
