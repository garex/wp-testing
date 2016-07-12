(function(angular) {
    'use strict';

    angular.module('wptApp').factory('QuestionCollection', QuestionCollectionFactory);

    QuestionCollectionFactory.$inject = ['BaseCollection', 'BaseOwnerable', 'AnswerCollection'];
    function QuestionCollectionFactory   (BaseCollection,   BaseOwnerable,   AnswerCollection) {
        function Question(title) {
            this.id      = null;
            this.title   = title || null;
            this.setAnswers([]);
        };
        Question.prototype = angular.extend({}, BaseOwnerable.prototype);
        Question.prototype.getClassName = function() {
            return 'Question';
        };
        Question.prototype.setAnswers = function(answersArray) {
            this.answers = AnswerCollection.fromArray(answersArray);
            this.answers.owner(this);
            var question = this;
            this.answers.on('remove:item', function(answer, $index) {
                question.owner().trigger('remove:answer', answer, $index);
            });
            return this;
        };

        function QuestionCollection() {
            var defaultAnswers = AnswerCollection.fromArray([]);
            this.defaultAnswers = function () {
                return defaultAnswers;
            };

            var globalAnswers = {};
            this.globalAnswers = function () {
                return globalAnswers;
            };
        };
        QuestionCollection.prototype = angular.extend({}, BaseCollection.prototype);
        QuestionCollection.fromArray = BaseCollection.fromArrayGenerator(QuestionCollection);
        QuestionCollection.prototype.fromArray = QuestionCollection.fromArray;
        QuestionCollection.prototype.addGlobalAnswer = function(id, title) {
            this.defaultAnswers().addNew(null, id);
            this.globalAnswers()[id] = title;
            return this;
        };
        QuestionCollection.prototype.create = function(title) {
            var question = new Question(title);
            return question.owner(this).setAnswers(this.defaultAnswers().copy());
        };
        QuestionCollection.prototype.createFromObject = function(object) {
            var question = BaseCollection.prototype.createFromObject.apply(this, arguments);
            return question.owner(this).setAnswers(object.answers);
        };
        QuestionCollection.prototype.addFromText = function(text) {
            text.trim().split(/[\r\n]+/).forEach(function (title) {
                this.addNew(title.replace(/^\w{1,3}[^\w\s]\s+/, '').trim());
            }, this);
            return this;
        };
        QuestionCollection.prototype.injectScales = function(scales) {
            var questions = this;
            questions.forEachLive(function(question) {
                question.answers.forEachLive(function(answer) {
                    scales.forEach(function(scale) {
                        var score = answer.scoresData().addNew(answer, scale);
                        scale.addScore(score);
                    });
                });
            });
            scales.refresh();
            return this;
        };
        QuestionCollection.prototype.forEachScores = function(callback) {
            var $indexes = {};
            this.forEach(function(question, $questionIndex) {
                $indexes.questionIndex = $questionIndex;
                question.answers.forEachScores(callback, $indexes);
            });
        };

        return QuestionCollection;
    };
})(angular);
