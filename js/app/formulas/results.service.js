(function(angular, Wpt) {
    'use strict';

    angular.module('wptApp').factory('resultsService', resultsService);

    resultsService.$inject = ['ResultCollection'];
    function resultsService   (ResultCollection) {
        var results = ResultCollection.fromArray(Wpt.results);

        return results;
    };
})(angular, Wpt);
