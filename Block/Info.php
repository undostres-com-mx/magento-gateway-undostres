<?php

namespace unDosTres\paymentGateway\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

class Info extends ConfigurableInfo
{
    /* UTILITY GET LABEL FOR BLOCKS */
    protected function getLabel($field)
    {
        return __($field);
    }
}
