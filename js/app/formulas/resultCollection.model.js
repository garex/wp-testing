(function(angular) {
    'use strict';

    angular.module('wptApp').factory('ResultCollection', ResultCollectionFactory);

    ResultCollectionFactory.$inject = ['BaseCollection', 'BaseOwnerable', 'Formula'];
    function ResultCollectionFactory   (BaseCollection,   BaseOwnerable,   Formula) {
        function Result() {
            this.id = null;
            this.title = null;
            this.tooltip = null;
            this.editLink = null;
            this.setFormula(new Formula());
        };
        Result.prototype = angular.extend({}, BaseOwnerable.prototype);
        Result.prototype.setFormula = function(formula) {
            this.formula = formula;

            return this;
        };

        function ResultCollection() {
            this.formulas = {};

            this.on('fromArray:add:item', function(result) {
                this.formulas[result.id] = result.formula;
            });
        };
        ResultCollection.prototype = angular.extend({}, BaseCollection.prototype);
        ResultCollection.fromArray = BaseCollection.fromArrayGenerator(ResultCollection);
        ResultCollection.prototype.fromArray = ResultCollection.fromArray;
        ResultCollection.prototype.create = function() {
            var result = new Result();

            return result.owner(this);
        };
        ResultCollection.prototype.createFromObject = function(object) {
            var result = BaseCollection.prototype.createFromObject.apply(this, arguments);

            return result.owner(this).setFormula(Formula.fromObject(object.formula));
        };

        return ResultCollection;
    };
})(angular);
