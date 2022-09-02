/* JS GATEWAY ACTIONS */
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component, url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Undostres_PaymentGateway/payment/form'
            },
            redirectAfterPlaceOrder: false,
            getCode: function () {
                return window.checkoutConfig.payment.Undostres_Gateway.code;
            },
            getUndostresLogo: function () {
                return window.checkoutConfig.payment.Undostres_Gateway.logo;
            },
            /* CALLS THE FUNCTION THAT MAKE THE PAYMENT URL  */
            afterPlaceOrder: function () {
                window.location.replace(url.build('Undostres/Checkout/Index'));
            }
        });
    }
);
