<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Payment\Gateway\Config\Config as MagentoConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/* EXTRACTS INFO FROM MAGENTO CONFIG THROUGH CONFIG EXTENDED CLASS - DICTIONARY */

class Config extends MagentoConfig
{
    const CODE = "Undostres_Gateway";

    private $pathPattern;
    private $methodCode;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->methodCode = $methodCode;
        $this->pathPattern = $pathPattern;
    }

    /**
     * @param string $field
     * @param mixed|null $default
     * @param int|string|null $storeId
     *
     * @return mixed|null
     */
    public function getConfigValue(string $field, $default = null, $storeId = null)
    {
        if (!$storeId) {
            $storeId = $this->storeHelper->getStoreId();
        }
        $value = parent::getValue($field, $storeId);
        if ($value === null) {
            $value = $default;
        }
        return $value;
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
