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
                type: 'undostres_gateway',
                component: 'undostres_gateway/js/view/payment/method-renderer/undostres_gateway'
            }
        );
        return Component.extend({});
    }
);
