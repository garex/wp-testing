(function(angular) {
    'use strict';

    angular.module('wptApp').factory('Formula', FormulaFactory);

    FormulaFactory.$inject = [];
    function FormulaFactory () {
        function Formula(id, source) {
            this.id     = id;
            this.source = source;
        };
        Formula.fromObject = function(object) {
            return new Formula(object.id, object.source);
        };

        return  Formula;
    };
})(angular);
