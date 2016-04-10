(function(angular) {
    'use strict';

    angular.module('wptApp').controller('EditQuestionTreeController', EditQuestionTreeController);

    EditQuestionTreeController.$inject = ['$scope', 'questionTreeService', 'questionsService', 'scalesService'];
    function EditQuestionTreeController   ($scope,   questionTreeService,   questionsService,   scalesService) {
        $scope.tree         = questionTreeService;
        $scope.questions    = questionsService;
        $scope.scales       = scalesService;
    };
})(angular);
