(function(angular, templateEl, __) {
    'use strict';

    var template = '<div class="notice inline notice-warning notice-alt">Formula toolbar is broken!</div>';
    if (templateEl) {
        template = templateEl.innerHTML
        templateEl.innerHTML = '';
    }

    angular.module('wptApp').directive('wptFormulas', wptFormulas);
    angular.module('wptApp').directive('wptFormulaToolbar', wptFormulaToolbar);
    angular.module('wptApp').directive('wptFormulaInput', wptFormulaInput);

    function wptFormulas() {
        return {
            restrict : 'A',
            controller: function ($scope) {
                var activeIndex = null,
                    activeElement = null;

                $scope.isActive = this.isActive = function(index) {
                    return activeIndex === index;
                };
                this.runAction = function(action) {
                    var caretCtrl   = activeElement.controller('caretAware'),
                        selection   = caretCtrl ? caretCtrl.getSelection() : {text: '', length: 0},
                        insertValue = action.getSource().replace('{selection}', selection.text),
                        isSelected  = selection.length > 0;

                    if (isSelected) {
                        caretCtrl.setSelectionText(insertValue);
                    } else {
                        var value       = activeElement.val(),
                            isEmpty     = value.length == 0,
                            endsOnSpace = value.substring(value.length - 1) == ' ',
                            prefix      = (isEmpty || endsOnSpace) ? '' : ' ';

                        activeElement.val(value + prefix + insertValue + ' ');
                    }

                    activeElement.change().focus();
                };
                this.activate = function(scopeIndex, scopeElement) {
                    if (this.isActive(scopeIndex)) {
                        return;
                    }
                    activeIndex = scopeIndex;
                    activeElement = scopeElement;
                };
            },
        };
    };

    function Action(source, label, tooltip) {
        this.source = source;
        this.label = label;
        this.tooltip = tooltip;
    };
    Action.prototype.getTooltip = function() {
        return this.tooltip;
    };
    Action.prototype.getLabel = function() {
        return this.label;
    };
    Action.prototype.getSource = function() {
        return this.source;
    };

    function QuestionAnswerAction() {
        this.question = 1;
        this.answer = 1;
    };
    QuestionAnswerAction.prototype.getSource = function() {
        return 'question_' + this.question + '_answer_' + this.answer;
    };

    function createActionCollection(sources, tooltip) {
        var actions = [];

        sources.forEach(function (source) {
            var label = source.replace('{selection}', '...');
            actions.push(new Action(source, label, tooltip));
        });

        return actions;
    };

    var comparisions = createActionCollection(['<', '>', '<=', '=>', '<>', '=', 'AND', 'OR', '( {selection} )', 'NOT ( {selection} )'], __.comparision);
    var operators = createActionCollection(['+', '-', '*', '/'], __.operator);

    wptFormulaToolbar.$inject = ['variablesService', 'questionsService'];
    function wptFormulaToolbar(   variablesService,   questionsService) {
        return {
            restrict : 'E',
            replace: true,
            require: '^wptFormulas',
            scope: {
                isVariableAvailableFn: '&'
            },
            controller: function ($scope, $element, $attrs) {
                $scope.isDisabled = ($attrs['disabled'] || false);
                $scope.comparisions = comparisions;
                $scope.operators = operators;
                $scope.variables = variablesService;
                $scope.questionAnswerAction = new QuestionAnswerAction();

                function isFunction(x) {
                    return Object.prototype.toString.call(x) == '[object Function]';
                };

                $scope.isVariableAvailable = isFunction($scope.isVariableAvailableFn())
                    ? $scope.isVariableAvailableFn()
                    : function(variable, $index) {
                        return true;
                    };

                var maxAnswersCount = calculateMaxAnswersCount();
                function calculateMaxAnswersCount() {
                    var result = 0;

                    questionsService.forEach(function (question) {
                        result = Math.max(result, question.answers.length);
                    });

                    return result;
                };
                questionsService.on('add:item', function(question) {
                    maxAnswersCount = calculateMaxAnswersCount();
                });
                questionsService.forEachLive(function(question) {
                    question.answers.on('changed:item', function(answer) {
                        maxAnswersCount = calculateMaxAnswersCount();
                    })
                });

                $scope.hasVariables = function() {
                    return variablesService.length > 0 || $scope.isQuestionAnswerActive()
                };
                $scope.isQuestionAnswerActive = function() {
                    return questionsService.length > 0 && maxAnswersCount > 0;
                };
                $scope.maxQuestionsCount = function() {
                    return questionsService.length;
                };
                $scope.maxAnswersCount = function() {
                    return maxAnswersCount;
                };
            },
            link: function($scope, $element, $attr, formulaController) {
                $scope.isActive = formulaController.isActive;
                $scope.runAction = formulaController.runAction;
            },
            template: template
        };
    };

    function wptFormulaInput() {
        return {
            restrict : 'A',
            require: '^wptFormulas',
            link: function($scope, $element, $attr, formulaController) {
                if (0 == $scope.$index) {
                    formulaController.activate($scope.$index, $element);
                }

                $element.bind('focus', function() {
                    formulaController.activate($scope.$index, $element);
                });
            }
        };
    };
})(angular, document.getElementById('wptFormulaToolbarDirectiveTemplate'), Wpt.locale);
