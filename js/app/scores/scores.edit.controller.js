(function(angular, Wpt) {
    'use strict';

    angular.module('wptApp').controller('EditScoresController', EditScoresController);

    EditScoresController.$inject = ['$scope', 'questionsService', 'scalesService'];
    function EditScoresController   ($scope,   questionsService,   scalesService) {
        $scope.questions        = questionsService;
        $scope.scales           = scalesService;
        $scope.questionMaxWidth = calculateQuestionMaxWidth(scalesService);

        var renderStep = 5;
        $scope.startIndex       = 0;
        $scope.endIndex         = renderStep;

        function calculateQuestionMaxWidth(scales) {
            var inputWidthEm  = 5,
                cellPaddingEm = 0.77 * 2,
                cellWidthEm   = inputWidthEm + cellPaddingEm,
                answerCells   = 2;

            return cellWidthEm * (scales.length + answerCells) + 'em';
        };

        var focused = {};
        $scope.isQuestionSelected = function (question) {
            if (typeof focused.Question === 'undefined' && question === $scope.questions[0]) {
                return true;
            }
            return this.isFocused(question);
        };
        $scope.isFocused = function (object) {
            return focused[object.getClassName()] === object;
        };
        function focus(object) {
            focused[object.getClassName()] = object;
        };
        $scope.focus = function (score, questionIndex) {
            focus(score.scale);
            focus(score.answer);
            focus(score.answer.question());
            $scope.startIndex = questionIndex - renderStep;
            $scope.endIndex   = questionIndex + renderStep;
        };
    };
})(angular, Wpt);
