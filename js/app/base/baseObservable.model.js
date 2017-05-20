(function(angular, asEvented) {
    'use strict';

    angular.module('wptApp').factory('BaseObservable', BaseObservableFactory);

    function BaseObservableFactory() {
        return BaseObservable;
    };

    function BaseObservable() {
    };
    BaseObservable.prototype.toJSON = function () {
        var result = {};

        for (var key in this) {
            if ('events' == key) {
                continue;
            }
            result[key] = this[key];
        }

        return result;
    };

    asEvented.call(BaseObservable.prototype);
})(angular, asEvented);
