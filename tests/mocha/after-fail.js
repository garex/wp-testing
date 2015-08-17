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

        var fullName = path + '/' + nextScreenIndex() + '-' + normalizeFilename(getFullTitle(this)) + '.screen.png',
            options  = {
                width   : 1280,
                height  : 850,
                top     : 0,
                left    : 0
            }

        casper.capture(fullName, options)
        console.log('       ' + casper.getCurrentUrl())
    })
}
