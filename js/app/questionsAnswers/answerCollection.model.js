(function(angular) {
    'use strict';

    angular.module('wptApp').factory('AnswerCollection', AnswerCollectionFactory);

    AnswerCollectionFactory.$inject = ['BaseCollection', 'BaseOwnerable', 'ScoreCollection'];
    function AnswerCollectionFactory   (BaseCollection,   BaseOwnerable,   ScoreCollection) {
        function Answer(title, globalAnswerId) {
            this.id               = null;
            this.title            = title || null;
            this.global_answer_id = globalAnswerId || null;
            this.scores           = {};

            var scores            = ScoreCollection.fromArray([])
            this.scoresData       = function() {
                return scores;
            };
        };
        Answer.prototype = angular.extend({}, BaseOwnerable.prototype);
        Answer.prototype.copy = function() {
            var copy    = new Answer();
            copy.id     = this.id;
            copy.title  = this.title;
            copy.global_answer_id = this.global_answer_id;
            return copy;
        };
        Answer.prototype.getClassName = function() {
            return 'Answer';
        };
        Answer.prototype.getTitle = function() {
            if (null == this.global_answer_id || this.title != null) {
                return this.title;
            }
            var globalAnswers = this.owner().owner().owner().globalAnswers();
            return globalAnswers[this.global_answer_id];
        };
        Answer.prototype.question = function() {
            return this.owner().owner();
        };

        function AnswerCollection() {
            this.on('owned', function(question) {
                var me = question.answers;
                question.on('remove', function() {
                    while (me.length) {
                        me.remove(me.length - 1);
                    }
                });
            });
        };
        AnswerCollection.prototype = angular.extend({}, BaseCollection.prototype, BaseOwnerable.prototype);
        AnswerCollection.fromArray = BaseCollection.fromArrayGenerator(AnswerCollection);
        AnswerCollection.prototype.fromArray = AnswerCollection.fromArray;
        AnswerCollection.prototype.create = function(title, globalAnswerId) {
            var answer = new Answer(title, globalAnswerId);
            return answer.owner(this);
        };
        AnswerCollection.prototype.createFromObject = function(object) {
            var answer = BaseCollection.prototype.createFromObject.apply(this, arguments);
            return answer.owner(this);
        };
        AnswerCollection.prototype.forEachScores = function(callback, $indexes) {
            $indexes = $indexes || {};
            this.forEach(function(answer, $answerIndex) {
                $indexes['answerIndex'] = $answerIndex;
                answer.scoresData().forEach(function(score) {
                    $indexes['scoreIndex'] = $indexes['scoreIndex'] || 0;
                    callback.call(score, score, $indexes);
                    $indexes['scoreIndex']++;
                });
            });
        };

        return AnswerCollection;
    };
})(angular);
