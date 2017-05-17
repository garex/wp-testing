(function(angular) {
    'use strict';

    angular.module('wptApp').factory('VariableCollection', VariableCollectionFactory);

    VariableCollectionFactory.$inject = ['BaseCollection'];
    function VariableCollectionFactory   (BaseCollection) {
        function Variable() {
            this.title = null;
            this.typeLabel = null;
            this.source = null;
        };
        Variable.prototype.getTooltip = function() {
            return this.typeLabel;
        };
        Variable.prototype.getLabel = function() {
            return this.title;
        };
        Variable.prototype.getSource = function() {
            return this.source;
        };

        function VariableCollection() {
        };
        VariableCollection.prototype = angular.extend({}, BaseCollection.prototype);
        VariableCollection.fromArray = BaseCollection.fromArrayGenerator(VariableCollection);
        VariableCollection.prototype.fromArray = VariableCollection.fromArray;
        VariableCollection.prototype.create = function() {
            return new Variable();
        };

        return VariableCollection;
    };
})(angular);
