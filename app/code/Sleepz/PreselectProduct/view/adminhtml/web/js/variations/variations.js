// jscs:disable requireDotNotation
// jscs:disable jsDoc
define([
    'Magento_ConfigurableProduct/js/variations/variations',
    'jquery',
    'ko',
    'underscore',
    'Magento_Ui/js/modal/alert'
], function (variationsComponent, $, ko, _, alert) {
    'use strict';

    function UserException(message)
    {
        this.message = message;
        this.name = 'UserException';
    }
    UserException.prototype = Object.create(Error.prototype);

    return variationsComponent.extend({
        checkDefault: function (variation) {
            if (variation.default) {
                return ko.observable(variation.productId);
            }
            return ko.observable();
        }
    });
});
