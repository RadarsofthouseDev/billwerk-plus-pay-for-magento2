define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url'
    ],
    function (Component,url) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Radarsofthouse_Reepay/payment/form_verkkopankki',
            },
            
            redirectAfterPlaceOrder: false,

            getCode: function() {
                return 'reepay_verkkopankki';
            },

            afterPlaceOrder: function() {
                window.location.replace(url.build("reepay/standard/redirect"));
            }
        });
    }
);