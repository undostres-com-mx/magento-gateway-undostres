/* ADDING GATEWAY TO PAYMENT LISTS */
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'undostres-gateway',
                component: 'magento-gateway-undostres/js/view/payment/method-renderer/undostres-gateway'
            }
        );
        return Component.extend({});
    }
);
