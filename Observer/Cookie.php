<?php

namespace Undostres\PaymentGateway\Observer;

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
        $cookieName = 'UDT';
        $cookiePositiveValue = 'isUDT';
        $cookieNegativeValue = 'notUDT';
        $queryParam = $this->_request->getParam('udtref');
        $cookieValue = $this->_cookieManager->getCookie($cookieName);
        $expiresAfter = (30 * 86400);

        $isQueryParamSet = ($queryParam !== null);
        $cookieNotSetOrNegative = (
            $cookieValue === null || $cookieValue == $cookieNegativeValue
        );

        if ($cookieNotSetOrNegative) $newCookieValue = $isQueryParamSet ?
            $cookiePositiveValue : $cookieNegativeValue;
        else $newCookieValue = $cookiePositiveValue;

        $newCookieMetadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration($expiresAfter)
            ->setPath('/');
        $this->_cookieManager->setPublicCookie(
            $cookieName, $newCookieValue, $newCookieMetadata
        );
    }
}
