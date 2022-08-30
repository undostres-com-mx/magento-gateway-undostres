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
                type: 'Undostres_Gateway',
                component: 'Undostres_Gateway/js/view/payment/method-renderer/Undostres_Gateway'
            }
        );
        return Component.extend({});
    }
);
