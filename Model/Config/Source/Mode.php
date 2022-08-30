<?php

namespace Undostres\paymentGateway\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Country\Collection;
use Magento\Directory\Model\Config\Source\Country;

class Mode
{
    private  $_modesCollection;

    /* SHOW ALL THE OPTIONS */
    public function __construct()
    {
        $modesCollection = array('production' => 'Produccion',
                                 'testing'    => 'Testing',
                                 'nobugs'     => 'Testing no bugs',
                                 'qa01'       => 'Testing qa01',
                                 'localhost'  => 'Localhost');
       $this->_modesCollection =  $modesCollection;
    }

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return $this->_modesCollection;
    }
}
