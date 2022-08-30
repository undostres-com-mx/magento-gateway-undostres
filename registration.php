<?php

use Magento\Framework\Component\ComponentRegistrar as R;

/*
 * PLUGIN REGISTER
 * Undostres_Gateway -> Gateway name
 * Undostres_Gateway -> Plugin name
*/

R::register(R::MODULE, 'unDosTres_paymentGateway', __DIR__);
