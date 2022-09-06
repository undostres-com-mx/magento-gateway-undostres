<?php

namespace Undostres\PaymentGateway\Model;

use Magento\Payment\Gateway\Config\Config as MagentoConfig;

/* EXTRACTS INFO FROM MAGENTO CONFIG THROUGH CONFIG EXTENDED CLASS - DICTIONARY */

class Config extends MagentoConfig
{
    const CODE = 'Undostres_Gateway';

    public function getTitle()
    {
        return $this->getValue('title');
    }

    public function isActive()
    {
        return $this->getValue('active');
    }

    public function getKey()
    {
        return $this->getValue('key');
    }

    public function getUrl()
    {
        return $this->getValue('url');
    }

    public function isLogging()
    {
        return $this->getValue('log');
    }
}
