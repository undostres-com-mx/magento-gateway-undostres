<?php

namespace Undostres\PaymentGateway\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\Config\Source\Country;

class RestrictedCountry extends Country
{
    /* ONLY SHOW MX */
    public function __construct(Collection $countryCollection)
    {
        $countryCollection->addCountryIdFilter(array('MX'));
        parent::__construct($countryCollection);
    }
}
