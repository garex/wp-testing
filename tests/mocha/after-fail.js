function getFullTitle(from) {
    var title       = [],
        currentTest = from.currentTest

    while (true) {
        title.unshift(currentTest.title)
        if (typeof currentTest.parent == 'undefined') {
            break;
        }
        currentTest = currentTest.parent
    }

    return title.join("\t")
}

function normalizeFilename(text) {
    return text
        .replace(/\t+/gi, '--')
        .replace(/[^a-z\d\-]+/gi, '-')
        .replace(/^-+/, '')
        .replace(/-+$/, '')
}

function nextScreenIndex() {
    ++screenIndex

    return (screenIndex < 10) ? '0' + screenIndex : screenIndex
}

var path        = require('./env').screenshots(),
    screenIndex = 0;

module.exports.screenshots = function () {
    casper.options.viewportSize = {width: 1280, height: 850}

    afterEach('Take a screenshot', function () {
        if (this.currentTest.state != 'failed') {
            return
        }

        module.exports.screenshot(getFullTitle(this));
    })
}

module.exports.screenshot = function takeScreenshot(fullTitle) {
    var fullName = path + '/' + nextScreenIndex() + '-' + normalizeFilename(fullTitle) + '.screen.png',
    maxHeight = casper.evaluate(function() {
        return (document.getElementById('wpwrap') || document.body).clientHeight;
    }),
    options  = {
        width   : 1280,
        height  : maxHeight,
        top     : 0,
        left    : 0
    }

    casper.capture(fullName, options)
    console.log('       Url: ' + casper.getCurrentUrl())
    console.log('       Screen: ' + fullName)
};
