(function appModule(angular, webshims, document, locale) {
    'use strict';

    webshims.polyfill('es5');

    angular.module('wptApp', [
        'leodido.caretAware'
    ]).constant('locale',  locale);
})(angular, webshims, document, Wpt.locale);