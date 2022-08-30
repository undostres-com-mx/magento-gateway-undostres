<?php

namespace Undostres\paymentGateway\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class DisableGateways implements ObserverInterface
{

    protected $_cookieManager;

    public function __construct(\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager)
    {
        $this->_cookieManager = $cookieManager;
    }
    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $result          = $observer->getEvent()->getResult();
        $method_instance = $observer->getEvent()->getMethodInstance();
        //$quote           = $observer->getEvent()->getQuote();

        $cookieValue = $this->_cookieManager->getCookie('UDT');


        /* Disable */
        if ($cookieValue == "isUDT" && $method_instance->getCode() != 'undostres-gateway') {
            $result->setData('is_available', false);
        } else if ($cookieValue != "isUDT" && $method_instance->getCode() == 'undostres-gateway') {
            $result->setData('is_available', false);
        }
    }
}
