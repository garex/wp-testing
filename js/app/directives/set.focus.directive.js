(function(angular) {
    'use strict';

    angular.module('wptApp').directive('wptSetFocus', wptSetFocus);

    function wptSetFocus() {
        return {
            restrict : 'A',
            scope    : {wptSetFocus: '='},
            link     : function(scope, element){
                if(!scope.wptSetFocus) {
                    return;
                }
                scope.$evalAsync(function() {
                    element[0].focus();
                });
            }
        };
    };
})(angular);
