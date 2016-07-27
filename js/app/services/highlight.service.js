(function(angular) {
    'use strict';

    angular.module('wptApp').factory('highlight', highlight);

    highlight.$inject = ['$document'];
    function highlight($document) {
        return function(id) {
            var target = $document.find(id);
            target.css('backgroundColor', 'infobackground');
            setTimeout(function() {
                target.css('backgroundColor', '');
            }, 800);
        };
    };
})(angular);
