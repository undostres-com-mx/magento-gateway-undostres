<?php

namespace Undostres\PaymentGateway\Observer;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Event\Observer;

/* GATEWAYS DISABLER ON COOKIE */

class DisableGateways implements ObserverInterface
{
    protected $cookieManager;

    public function __construct(CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    public function execute(Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        $cookieValue = $this->cookieManager->getCookie('UDT');
        if (($cookieValue == "isUDT" && $method_instance->getCode() != 'undostres_gateway') || ($cookieValue != "isUDT" && $method_instance->getCode() == 'undostres_gateway'))
            $result->setData('is_available', false);
    }
}
