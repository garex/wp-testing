(function(angular, asEvented) {
    'use strict';

    angular.module('wptApp').factory('BaseObservable', BaseObservableFactory);

    function BaseObservableFactory() {
        return BaseObservable;
    };

    function BaseObservable() {
    };

    asEvented.call(BaseObservable.prototype);
})(angular, asEvented);
