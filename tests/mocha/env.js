var server = require('system').env.WP_T_SERVER || 'http://wpti.dev:8000'

module.exports.server = function () {
    return server
};
