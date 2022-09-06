<?php

namespace Undostres\PaymentGateway\Model;

//use Magento\Payment\Gateway\Config\Config as MagentoConfig;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/* EXTRACTS INFO FROM MAGENTO CONFIG THROUGH CONFIG EXTENDED CLASS - DICTIONARY */

class Config
{
    const CODE = "Undostres_Gateway";

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(Context $context, ScopeConfigInterface $scopeConfig, array $data = [])
    {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $data);
    }

    private function getConfigValue($value)
    {
        return $this->scopeConfig->getValue("payment/Undostres_Gateway/" . $value, ScopeInterface::SCOPE_STORE);
    }

    public function getTitle()
    {
        return $this->getConfigValue("title");
    }

    public function isActive()
    {
        return $this->getConfigValue("active");
    }

    public function getKey()
    {
        return $this->getConfigValue("key");
    }

    public function getUrl()
    {
        return $this->getConfigValue("url");
    }

    public function isLogging()
    {
        return $this->getConfigValue("log");
    }
}
