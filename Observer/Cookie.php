<?php

namespace unDosTres\paymentGateway\Observer;

use Magento\Framework\Event\ObserverInterface;

class Cookie implements ObserverInterface
{

    protected $_cookieManager;

    public function __construct(
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->_request = $request;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $UDTParam = $this->_request->getParam('udtref');
        $cookieValue = $this->_cookieManager->getCookie('UDT');

        $isUDT = isset($UDTParam);
        $time = (30 * 86400);
        if (!isset($cookieValue) || (isset($cookieValue) && $cookieValue == 'notUDT')) setcookie("UDT", $isUDT ? 'isUDT' : 'notUDT', time() + ($time), "/");
        else setcookie("UDT", 'isUDT', time() + ($time), "/");
    }
}
