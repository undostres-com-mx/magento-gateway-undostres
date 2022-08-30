<?php

use Magento\Framework\Component\ComponentRegistrar as R;

/*
 * PLUGIN REGISTER
 * undostres-gateway -> Gateway name
 * magento-gateway-undostres -> Plugin name
*/

R::register(R::MODULE, 'magento-gateway-undostres', __DIR__);
