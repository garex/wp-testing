(function(angular, Wpt) {
    'use strict';

    angular.module('wptApp').factory('scalesService', scalesService);

    scalesService.$inject = ['ScaleCollection'];
    function scalesService   (ScaleCollection) {
        var scales = ScaleCollection.fromArray(Wpt.scales);

        return scales;
    };
})(angular, Wpt);
