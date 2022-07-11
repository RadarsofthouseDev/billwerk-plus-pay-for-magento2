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
                type: 'reepay_blik',
                component: 'Radarsofthouse_Reepay/js/view/payment/method-renderer/reepay_blik'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
