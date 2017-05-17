(function(angular, Wpt) {
    'use strict';

    angular.module('wptApp').factory('variablesService', variablesService);

    variablesService.$inject = ['VariableCollection'];
    var instance = null;
    function variablesService   (VariableCollection) {
        if (instance == null) {
            instance = VariableCollection.fromArray(Wpt.variables);
        }

        return instance;
    };
})(angular, Wpt);
