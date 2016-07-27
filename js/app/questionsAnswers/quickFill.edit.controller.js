(function(angular) {
    'use strict';

    angular.module('wptApp').controller('EditQuickFillController', EditQuickFillController);

    EditQuickFillController.$inject = ['$scope'];
    function EditQuickFillController   ($scope) {
        angular.extend($scope, {
            visible: false,
            content: '',
            toggle: function() {
                this.visible = !this.visible;
            },
            process: function(items) {
                items.addFromText(this.content);
                this.visible = false;
                this.content = '';
            }
        });
    };
})(angular);
