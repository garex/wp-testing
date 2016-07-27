(function(angular) {
    'use strict';

    angular.module('wptApp').factory('ScoreCollection', ScoreCollectionFactory);

    ScoreCollectionFactory.$inject = ['BaseCollection', 'BaseObservable'];
    function ScoreCollectionFactory   (BaseCollection,   BaseObservable) {
        function Score(answer, scale) {
            this.answer = answer;
            this.scale  = scale;

            if (typeof this.answer === 'undefined') {
                return;
            }

            var me = this;
            this.answer.on('remove', function() {
                me.value(0);
            });
        };
        Score.prototype = angular.extend({}, BaseObservable.prototype);
        Score.prototype.getClassName = function() {
            return 'Score';
        };
        Score.prototype.value = function(newValue) {
            if (typeof this.answer === 'undefined') {
                return arguments.length ? this : undefined;
            }
            if (!arguments.length) {
                return this.answer.scores[this.scale.id];
            }
            var oldValue = this.answer.scores[this.scale.id];
            this.answer.scores[this.scale.id] = newValue;
            if (newValue != oldValue) {
                this.trigger('change:value', newValue, oldValue);
            }
            return this;
        };
        Score.prototype.valueAsFloat = function() {
            var value = parseFloat(this.value());
            return isNaN(value) ? 0 : value;
        };

        function ScoreCollection() {

        };
        ScoreCollection.prototype = angular.extend({}, BaseCollection.prototype);
        ScoreCollection.fromArray = BaseCollection.fromArrayGenerator(ScoreCollection);
        ScoreCollection.prototype.fromArray = ScoreCollection.fromArray;
        ScoreCollection.prototype.create = function(answer, scale) {
            var score = new Score(answer, scale);
            return score;
        };
        ScoreCollection.prototype.createFromObject = function(object) {
            var score = BaseCollection.prototype.createFromObject.apply(this, arguments);
            return score;
        };

        return ScoreCollection;
    };
})(angular);
