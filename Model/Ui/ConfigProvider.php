<?php

namespace Undostres\PaymentGateway\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Customer\Model\Session;
use Magento\Backend\Model\Session\Quote;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\View\Asset\Repository;
use Undostres\PaymentGateway\Gateway\Config\Config;

final class ConfigProvider implements ConfigProviderInterface
{
    protected $_gatewayConfig;
    protected $_scopeConfigInterface;
    protected $customerSession;
    protected $_urlBuilder;
    protected $request;
    protected $_assetRepo;

    public function __construct(
        Config $gatewayConfig,
        Session $customerSession,
        Quote $sessionQuote,
        Context $context,
        Repository $assetRepo
    ) {
        $this->_gatewayConfig = $gatewayConfig;
        $this->_scopeConfigInterface = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->sessionQuote = $sessionQuote;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_assetRepo = $assetRepo;
    }

    public function getConfig()
    {
        $logoFile = $this->_gatewayConfig->getLogo();
        if (isset($logoFile) && strlen($logoFile) > 0) {
            $logo = '../pub/media/sales/store/logo/' . $logoFile;
        } else {
            /** @var $om \Magento\Framework\ObjectManagerInterface */
            $om = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var $request \Magento\Framework\App\RequestInterface */
            $request = $om->get('Magento\Framework\App\RequestInterface');
            $params = array();
            $params = array_merge(['_secure' => $request->isSecure()], $params);
            $logo = $this->_assetRepo->getUrlWithParams('magento-gateway-undostres::images/undostres_logo.png', $params);
        }

        $config = [
            'payment' => [
                Config::CODE => [
                    'title' => $this->_gatewayConfig->getTitle(),
                    'description' => $this->_gatewayConfig->getDescription(),
                    'logo' => $logo,
                    'allowed_countries' => $this->_gatewayConfig->getSpecificCountry(),
                ]
            ]
        ];

        return $config;
    }
}
