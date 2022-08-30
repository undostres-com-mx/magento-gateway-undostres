<?php

use Magento\Framework\Component\ComponentRegistrar as R;

/*
 * PLUGIN REGISTER
 * undostres_gateway -> Gateway name
 * magento_gateway_undostres -> Plugin name
*/

R::register(R::MODULE, 'magento_gateway_undostres', __DIR__);
