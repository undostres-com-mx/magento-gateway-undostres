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
                template: 'unDosTres_paymentGateway/payment/form'
            },
            redirectAfterPlaceOrder: false,
            initialize: function () {
                this._super();
                return this;
            },
            getCode: function () {
                return 'undostres_gateway';
            },
            getData: function () {
                return {
                    'method': this.item.method
                };
            },
            getTitle: function () {
                return window.checkoutConfig.payment.undostres_gateway.title;
            },
            getDescription: function () {
                return window.checkoutConfig.payment.undostres_gateway.description;
            },
            getunDosTresLogo: function () {
                return window.checkoutConfig.payment.undostres_gateway.logo;
            },
            /* CALLS THE FUNCTION THAT MAKE THE PAYMENT URL  */
            afterPlaceOrder: function () {
                window.location.replace(url.build('unDosTres/checkout/index'));
            }
        });
    }
);
