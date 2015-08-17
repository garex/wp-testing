var env         = require('system').env,
    server      = env.WP_T_SERVER || 'http://wpti.dev:8000',
    screenshots = env.CIRCLE_ARTIFACTS || '/tmp'
    multisite   = env.WP_T_MULTISITE == 1 || false

module.exports.server = function () {
    return server
}

module.exports.screenshots = function () {
    return screenshots
}

module.exports.multisite = function () {
    return multisite
}
