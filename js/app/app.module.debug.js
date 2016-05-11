(function(angular) {
    'use strict';

    angular.module('wptApp', ['digestHud'])
    .config(function(digestHudProvider) {
        digestHudProvider.enable();
        digestHudProvider.setHudPosition('bottom right');
    });
})(angular);
