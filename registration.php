<?php

/*
PLUGIN REGISTER
undostres_gateway -> Gateway name
unDosTres_paymentGateway -> Plugin name
*/
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'unDosTres_paymentGateway',
    __DIR__
);
