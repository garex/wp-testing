(function(angular) {
    'use strict';

    angular.module('wptApp').factory('questionTreeService', questionTreeService);

    questionTreeService.$inject = ['QuestionTree', 'scalesService', 'questionsService'];
    function questionTreeService   (QuestionTree,   scalesService,   questionsService) {
        var tree = new QuestionTree(questionsService);

        function scoreChanged(newValue, oldValue) {
            var score = this;

            if (oldValue) {
                tree.removeValue(score, oldValue);
            }
            if (newValue) {
                tree.setValue(score, newValue);
            }
        };

        scalesService.forEach(function(scale, scaleIndex) {
            tree.addScale(scale);
            scale.scores.forEach(function(score) {
                var scoreValue = score.value();
                score.on('change:value', scoreChanged);
                if (!scoreValue) {
                    return;
                }

                tree.setValue(score, scoreValue);
            });
            scale.scores.on('add:item', function(score) {
                score.on('change:value', scoreChanged);
            });
        });

        questionsService.on('remove:item', function(question, $index) {
            var questions = question.owner();
            if ($index == questions.length - 1) {
                return;
            }
            var questionIndex = $index + 1;
            questions.slice(questionIndex).forEachScores(function(score, $indexes) {
                var scoreValue = score.value();
                if (!scoreValue) {
                    return;
                }
                var oldQuestionIndex = questionIndex + $indexes.questionIndex,
                    newQuestionIndex = oldQuestionIndex - 1;

                tree.removeValue(score, scoreValue, undefined, oldQuestionIndex);
                tree.setValue(score, scoreValue, undefined, newQuestionIndex);
            });
        });

        questionsService.on('remove:answer', function(answer, $index) {
            var answers = answer.owner();
            if ($index == answers.length - 1) {
                return;
            }
            var answerIndex = $index + 1;
            answers.slice(answerIndex).forEachScores(function(score, $indexes) {
                var scoreValue = score.value();
                if (!scoreValue) {
                    return;
                }
                var oldAnswerIndex = answerIndex + $indexes.answerIndex,
                    newAnswerIndex = oldAnswerIndex - 1;

                tree.removeValue(score, scoreValue, oldAnswerIndex);
                tree.setValue(score, scoreValue, newAnswerIndex);
            });
        });

        return tree;
    };
})(angular);
