<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Payment\Gateway\Config\Config as MagentoConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

/* EXTRACTS INFO FROM MAGENTO CONFIG THROUGH CONFIG EXTENDED CLASS */

class Config extends MagentoConfig
{
    const CODE = "Undostres_Gateway";

    public function __construct(ScopeConfigInterface $scopeConfig, $gatewayCode = null)
    {
        parent::__construct($scopeConfig, $gatewayCode);
    }

    /**
     * @param string $field
     *
     * @return mixed|null
     */
    public function getConfigValue(string $field)
    {
        return parent::getValue($field);
    }

    public function getTitle()
    {
        return $this->getConfigValue("title");
    }

    public function isActive(): bool
    {
        return $this->getConfigValue("active") === "1";
    }

    public function getKey()
    {
        return $this->getConfigValue("key");
    }

    public function getUrl()
    {
        return $this->getConfigValue("url");
    }

    public function canLog(): bool
    {
        return $this->getConfigValue("log") === "1";
    }
}
