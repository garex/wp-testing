(function(angular) {
    'use strict';

    angular.module('wptApp').factory('ScaleCollection', ScaleCollectionFactory);

    ScaleCollectionFactory.$inject = ['BaseCollection', 'BaseOwnerable', 'ScoreCollection'];
    function ScaleCollectionFactory   (BaseCollection,   BaseOwnerable,   ScoreCollection) {
        function Scale(title) {
            this.id      = null;
            this.title   = title || null;
            this.scores  = ScoreCollection.fromArray([]);
            this.sum     = 0;
            this.maximum = 0;
        };
        Scale.prototype = angular.extend({}, BaseOwnerable.prototype);
        Scale.prototype.getClassName = function() {
            return 'Scale';
        };
        Scale.prototype.addScore = function(score) {
            this.scores.add(score);
            var me = this;
            score.on('change:value', function() {
                me.refresh();
            });
            return score;
        };
        Scale.prototype.refresh = function() {
            this.sum = this.maximum = 0;

            var me     = this,
                scores = {};
            this.scores.forEach(function(score) {
                var value = score.valueAsFloat();
                me.sum += value;

                var question = score.answer.question(),
                    hash     = question['$$hashKey'] || question.id;

                if (value <= 0) {
                    return;
                }

                scores[hash] = scores[hash] ? scores[hash] : 0;
                scores[hash] = value > scores[hash] ? value : scores[hash];
            });

            for (var hash in scores) {
                this.maximum += scores[hash];
            }

            return this;
        };

        function ScaleCollection() {
        };
        ScaleCollection.prototype = angular.extend({}, BaseCollection.prototype);
        ScaleCollection.fromArray = BaseCollection.fromArrayGenerator(ScaleCollection);
        ScaleCollection.prototype.fromArray = ScaleCollection.fromArray;
        ScaleCollection.prototype.create = function(title) {
            var scale = new Scale(title);
            return scale.owner(this);
        };
        ScaleCollection.prototype.createFromObject = function(object) {
            var scale = BaseCollection.prototype.createFromObject.apply(this, arguments);
            return scale.owner(this);
        };
        ScaleCollection.prototype.refresh = function() {
            this.forEach(function(scale) {
                scale.refresh();
            });
            return this;
        };

        return ScaleCollection;
    };
})(angular);
