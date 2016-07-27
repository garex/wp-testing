(function(angular, SortedMap) {
    'use strict';

    angular.module('wptApp').factory('QuestionTree', QuestionTreeFactory);

    QuestionTreeFactory.$inject = [];
    function QuestionTreeFactory () {
        function QuestionTree(questions) {
            this.map = new SortedMap();

            this.scaleIdToIndex = {};
            this.scalesCount = 0;

            this.globalAnswerIdToIndex = {};
            this.globalAnswerIndexToTitle = {};

            this.questions = questions;

            var me = this;
            questions.defaultAnswers().forEach(function(answer, index) {
                me.globalAnswerIdToIndex[answer.global_answer_id] = index;
                me.globalAnswerIndexToTitle[index] = questions.globalAnswers()[answer.global_answer_id];
            });
        };

        QuestionTree.prototype.entries = function() {
            return this.map.entries;
        };

        QuestionTree.prototype.isTall = function() {
            return this.map.size() > 4;
        };

        var scaleMultiplicator  = Math.pow(10, 3 * 3),
            scoreMultiplicator  = Math.pow(10, 3 * 2),
            answerMultiplicator = Math.pow(10, 3 * 1);

        QuestionTree.prototype.buildKey = function(scale, scoreValue, answerIndex, answerTitle) {
            var scaleIndex = this.scaleIdToIndex[scale.id],
                hashCode   = 0
                    + scaleIndex  * scaleMultiplicator
                    + scoreValue  * scoreMultiplicator
                    + answerIndex * answerMultiplicator
                ;

            return {
                scaleTitle  : scale.title,
                scaleIndex  : scaleIndex,
                scoreValue  : scoreValue,
                answerTitle : answerTitle,
                answerIndex : answerIndex,
                hashCode    : function() {
                    return hashCode;
                }
            };
        };

        QuestionTree.prototype.addScale = function(scale) {
            this.scaleIdToIndex[scale.id] = this.scalesCount++;
            return this;
        };

        QuestionTree.prototype.indexOfAnswer = function (answer) {
            if (answer.global_answer_id) {
                return this.globalAnswerIdToIndex[answer.global_answer_id];
            }
            return answer.owner().indexOf(answer);
        };

        QuestionTree.prototype.indexOfQuestion = function (question) {
            return question.owner().indexOf(question);
        };

        QuestionTree.prototype.processScore = function(score, scoreValue, callback, answerIndex, questionIndex) {
            var answerIndex = (typeof answerIndex === 'undefined') ? this.indexOfAnswer(score.answer) : answerIndex,
                answerTitle = (score.answer.global_answer_id) ? this.globalAnswerIndexToTitle[answerIndex] : answerIndex + 1,

                key         = this.buildKey(score.scale, scoreValue, answerIndex, answerTitle),
                valueMap    = this.map.get(key),

                question        = score.answer.question(),
                questionIndex   = (typeof questionIndex === 'undefined') ? this.indexOfQuestion(question) : questionIndex,
                questionNumber  = questionIndex + 1;

            return callback.call(this, key, valueMap, question, questionNumber);
        };

        QuestionTree.prototype.addNew = function(newScale, newScore, newAnswer) {
            if (typeof newScale === 'undefined' || typeof newScore === 'undefined' || typeof newAnswer === 'undefined') {
                return;
            }

            var answerIndex = newAnswer - 1,
                answerTitle = this.globalAnswerIndexToTitle[answerIndex];

            if (typeof answerTitle === 'undefined') {
                answerTitle = newAnswer;
            }

            var key = this.buildKey(newScale, newScore, answerIndex, answerTitle);
            if (!this.map.containsKey(key)) {
                var valueMap = new SortedMap();
                valueMap.joinedKeys = '';
                this.map.put(key, valueMap);
            }
        };

        QuestionTree.prototype.setValue = function(score, scoreValue, answerIndex, questionIndex) {
            return this.processScore(score, scoreValue, function(key, valueMap, question, questionNumber) {
                if (typeof valueMap === 'undefined') {
                    valueMap = new SortedMap();
                    this.map.put(key, valueMap);
                }

                valueMap.put(questionNumber, question);
                valueMap.joinedKeys = valueMap.keys.join(', ');
            }, answerIndex, questionIndex);
        };

        QuestionTree.prototype.removeValue = function(score, scoreValue, answerIndex, questionIndex) {
            return this.processScore(score, scoreValue, function(key, valueMap, question, questionNumber) {
                if (typeof valueMap === 'undefined') {
                    return;
                }

                valueMap.remove(questionNumber);
                valueMap.joinedKeys = valueMap.keys.join(', ');
            }, answerIndex, questionIndex);
        };

        QuestionTree.prototype.onValueChanged = function(treeEntry) {
            if (typeof treeEntry === 'undefined' || typeof treeEntry.value.joinedKeys === 'undefined') {
                return;
            }

            var originalMap   = treeEntry.value,
                scoreValue    = treeEntry.key.scoreValue,
                scoreMap      = new SortedMap(),
                addMap        = new SortedMap(),
                changedValues = treeEntry.value.joinedKeys.split(/[, ]+/).sort();

            treeEntry.value.keys.forEach(function(key) {
                scoreMap.put(key, 0);
            });

            changedValues.forEach(function(key) {
                key = key >> 0; // toInt
                if (key < 1) {
                    return;
                }
                if (originalMap.containsKey(key)) {
                    scoreMap.remove(key);
                } else {
                    scoreMap.put(key, scoreValue);
                }

            });

            var answerIndex = treeEntry.key.answerIndex,
                scaleIndex  = treeEntry.key.scaleIndex,
                me = this;
            scoreMap.entries.forEach(function(actionEntry) {
                var questionIndex  = actionEntry.key - 1,
                    scoreValue     = actionEntry.value;

                var score = me.questions
                    .getOrNull(questionIndex)
                    .answers
                    .getOrNull(answerIndex)
                    .scoresData()
                    .getOrNull(scaleIndex)
                ;

                score.value(scoreValue);
            });
        };

        return QuestionTree;
    };
})(angular, garex.SortedMap);
