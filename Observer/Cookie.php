<?php

namespace Undostres\PaymentGateway\Observer;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;

/* COOKIE SETTER AND UPDATER */

class Cookie implements ObserverInterface
{
    protected $cookieManager;
    protected $cookieMetadataFactory;
    protected $request;

    public function __construct(CookieManagerInterface $cookieManager, CookieMetadataFactory $cookieMetadataFactory, Http $request)
    {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        $isUDT = $this->_request->getParam('udtref') !== null;
        $cookie = $this->_cookieManager->getCookie("UDT");
        if (!$isUDT && ($cookie == null || $cookie == 'notUDT')) $value = "notUDT";
        else $value = "isUDT";
        $newCookieMetadata = $this->_cookieMetadataFactory->createPublicCookieMetadata()->setDuration(30 * 86400)->setPath('/');
        $this->_cookieManager->setPublicCookie('UDT', $value, $newCookieMetadata);
    }
}
