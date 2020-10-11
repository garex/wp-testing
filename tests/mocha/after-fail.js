casper.on('page.initialized', function (page) {
	page.evaluate(function() {
		if (!Function.prototype.bind) (function(){
		  var ArrayPrototypeSlice = Array.prototype.slice;
		  Function.prototype.bind = function(otherThis) {
		    if (typeof this !== 'function') {
		      // closest thing possible to the ECMAScript 5
		      // internal IsCallable function
		      throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
		    }

		    var baseArgs= ArrayPrototypeSlice.call(arguments, 1),
		        baseArgsLength = baseArgs.length,
		        fToBind = this,
		        fNOP    = function() {},
		        fBound  = function() {
		          baseArgs.length = baseArgsLength; // reset to default base arguments
		          baseArgs.push.apply(baseArgs, arguments);
		          return fToBind.apply(
		                 fNOP.prototype.isPrototypeOf(this) ? this : otherThis, baseArgs
		          );
		        };

		    if (this.prototype) {
		      // Function.prototype doesn't have a prototype property
		      fNOP.prototype = this.prototype;
		    }
		    fBound.prototype = new fNOP();

		    return fBound;
		  };
		})();
	});
});

casper.on('page.error', function(msg, trace) {
	this.echo('Error: ' + msg, 'ERROR')
	var msgStack = [];
	if (trace && trace.length) {
		trace.forEach(function(t) {
			msgStack.push(' -> ' + t.file + ': ' + t.line + (t.function ? ' (in function "' + t.function +'")' : ''))
		});
	}

	this.echo(msgStack.join('\n'), 'INFO')
});

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
