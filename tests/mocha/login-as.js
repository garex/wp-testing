var fs = require('fs')
var server = require('./env').server()

function cookiesRestore(file) {
    if (phantom.cookies.length == 0) {
        phantom.cookies = JSON.parse(fs.read(file))
        console.log('   === ============== === ')
        console.log('   === COOKIES RESTRD === ')
        console.log('   === ============== === ')
    }
};

function cookiesSave(file) {
    fs.write(file, JSON.stringify(phantom.cookies), 644)
};

function logOut(user) {
    var cookies = '/tmp/cookies.' + user + '.txt'

    casper.thenOpen(server + '/wp-login.php?action=logout', function() {
        this.clickLabel('log out', 'a')
    })

    casper.waitForUrl(/loggedout/, function() {
        if (fs.exists(cookies)) {
            fs.remove(cookies)
        }
    })
};

function loginAs(mocha, user, password, isForce, toServer) {
    var cookies = '/tmp/cookies.' + user + '.txt',
        exists  = fs.exists(cookies)

    if (!toServer) {
        toServer = server
    }

    mocha.timeout(3600000)

    exists && !isForce && cookiesRestore(cookies)

    var startUrl = isForce ? toServer + '/wp-login.php' : toServer + '/wp-admin/'
    casper.start(startUrl, function() {
        var isLoginForm = this.evaluate(function() {
            return typeof window.loginform !== 'undefined'
        })

        if (!isLoginForm) {
            cookiesSave(cookies)
            return
        }

        this.click('#rememberme')
        this.fill('form#loginform', {
            log : user,
            pwd : password
        }, true)
    })

    casper.waitForUrl(/admin/, function() {
        cookiesSave(cookies)
    })
};

module.exports.admin = function (mocha, isForce, toServer) {
    loginAs(mocha, 'wpti', 'wpti', isForce, toServer)
};

module.exports.adminLogout = function () {
    logOut('wpti')
};

module.exports.user = function (mocha) {
    casper.thenOpen(server + '/wp-login.php', {
        method: 'post',
        data  : {
            log: 'user',
            pwd: 'user'
        }
    }).waitForUrl(/admin/)
};

module.exports.userLogout = function () {
    logOut('user')
};
