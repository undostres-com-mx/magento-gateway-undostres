<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Undostres\PaymentGateway\Gateway\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    protected $_gatewayConfig;
    protected $_scopeConfigInterface;
    protected $customerSession;
    protected $_urlBuilder;
    protected $request;
    protected $_assetRepo;

    public function __construct(
        Config     $gatewayConfig,
        Session    $customerSession,
        Quote      $sessionQuote,
        Context    $context,
        Repository $assetRepo
    )
    {
        $this->_gatewayConfig = $gatewayConfig;
        $this->_scopeConfigInterface = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->sessionQuote = $sessionQuote;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_assetRepo = $assetRepo;
    }

    public function getConfig()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $om->get('Magento\Framework\App\RequestInterface');
        $params = array_merge(['_secure' => $request->isSecure()], []);
        $logo = $this->_assetRepo->getUrlWithParams('Undostres_PaymentGateway::images/undostres_logo.png', $params);

        /* THIS CONFIG IS PASSED TO JS */
        return [
            'payment' => [
                Config::CODE => [
                    'code' => Config::CODE,
                    'title' => $this->_gatewayConfig->getTitle(),
                    'logo' => $logo,
                ]
            ]
        ];
    }
}
