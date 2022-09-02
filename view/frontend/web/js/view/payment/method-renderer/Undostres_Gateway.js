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
            initialize: function () {
                this._super();
                return this;
            },
            getCode: function () {
                return window.checkoutConfig.payment.Undostres_Gateway.code;
            },
            getData: function () {
                return {
                    'method': this.item.method
                };
            },
            getTitle: function () {
                return window.checkoutConfig.payment.Undostres_Gateway.title;
            },
            getDescription: function () {
                return window.checkoutConfig.payment.Undostres_Gateway.description;
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
