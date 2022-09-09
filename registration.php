<?php

use Magento\Framework\Component\ComponentRegistrar as R;

/*
 * PLUGIN REGISTER
 * Undostres_Gateway -> GATEWAY NAME
 * Undostres_PaymentGateway -> PLUGIN NAME
*/
R::register(R::MODULE, 'Undostres_PaymentGateway', __DIR__);
