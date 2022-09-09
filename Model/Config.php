<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Payment\Gateway\Config\Config as MagentoConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;

/* BRINGS UNDOSTRES MAGENTO ADMIN CONFIG OF THE PLUGIN */

class Config extends MagentoConfig
{
    const CODE = "Undostres_Gateway";

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param null $gatewayCode
     */
    public function __construct(ScopeConfigInterface $scopeConfig, $gatewayCode = null)
    {
        parent::__construct($scopeConfig, $gatewayCode);
    }

    /**
     * @param string $field
     * @return mixed|null
     */
    private function getConfigValue(string $field)
    {
        return parent::getValue($field);
    }

    /**
     * @return mixed|null
     */
    public function getTitle()
    {
        return $this->getConfigValue("title");
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getConfigValue("active") === "1";
    }

    /**
     * @return mixed|null
     */
    public function getKey()
    {
        return $this->getConfigValue("key");
    }

    /**
     * @return mixed|null
     */
    public function getUrl()
    {
        return $this->getConfigValue("url");
    }

    /**
     * @return bool
     */
    public function canLog(): bool
    {
        return $this->getConfigValue("log") === "1";
    }
}
