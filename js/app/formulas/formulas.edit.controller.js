(function(angular) {
    'use strict';

    angular.module('wptApp').controller('EditFormulasController', EditFormulasController);

    EditFormulasController.$inject = ['$scope', 'resultsService'];
    function EditFormulasController   ($scope,   resultsService) {
        $scope.results = resultsService;
    };
})(angular);
