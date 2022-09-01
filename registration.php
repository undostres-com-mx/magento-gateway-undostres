<?php

use Magento\Framework\Component\ComponentRegistrar as R;

/*
 * PLUGIN REGISTER
 * Undostres_Gateway -> Gateway name
 * Undostres_PaymentGateway -> Plugin name
*/

R::register(R::MODULE, 'Undostres_PaymentGateway', __DIR__);
