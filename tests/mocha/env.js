var env         = require('system').env,
    server      = env.WP_T_SERVER || 'http://wpti.dev:8000',
    multiServer = env.WP_T_MULTI_SERVER || server,
    screenshots = env.CIRCLE_ARTIFACTS || '/tmp',
    multisite   = env.WP_T_MULTISITE == 1 || false,
    wpVersion   = env.WP_VERSION || 'latest'

module.exports.multiServer = function () {
    return multiServer
}

module.exports.server = function () {
    return server
}

module.exports.anotherServer = function (name) {
    return multiServer.replace('wpti.dev', name + '.wpti.dev')
}

module.exports.screenshots = function () {
    return screenshots
}

module.exports.multisite = function () {
    return multisite
}

function toVersion(version) {
    return (version + '.0.0.0').split('.').slice(0, 3).map(Number);
}

module.exports.isWp5Already = function () {
    if (wpVersion == 'latest') {
        return true;
    }

    return toVersion(wpVersion) >= toVersion('5.0.0');
}
