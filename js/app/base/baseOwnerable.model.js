(function(angular) {
    'use strict';

    angular.module('wptApp').factory('BaseOwnerable', BaseOwnerableFactory);

    BaseOwnerableFactory.$inject = ['BaseObservable'];
    function BaseOwnerableFactory   (BaseObservable) {
        function BaseOwnerable() {
        };
        BaseOwnerable.prototype = angular.extend({}, BaseObservable.prototype);
        BaseOwnerable.prototype.owner = function(newOwner) {
            if (typeof this.ownerData != 'undefined') {
                return this.ownerData(newOwner);
            }
            var owner = null,
            me    = this;
            this.ownerData = function(newOwner) {
                if (typeof newOwner == 'undefined') {
                    if (null == owner) {
                        throw new Error('Owner is not set');
                    }
                    return owner;
                }
                owner = newOwner;
                me.trigger('owned', owner);
                return me;
            };
            return this.ownerData(newOwner);
        };

        return BaseOwnerable;
    };
})(angular);
