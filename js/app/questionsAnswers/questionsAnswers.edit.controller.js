(function(angular) {
    'use strict';

    angular.module('wptApp').controller('EditQuestionsAnswersController', EditQuestionsAnswersController);

    EditQuestionsAnswersController.$inject = ['$scope', 'highlight', 'questionsService'];
    function EditQuestionsAnswersController   ($scope,   highlight,   questionsService) {
        $scope.questions = questionsService;
        $scope.highlight = highlight;
    };
})(angular);
