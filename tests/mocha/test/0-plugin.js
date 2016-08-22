if (require('../env').multisite()) {
    return
}

require('../after-fail').screenshots()

describe('Plugin activation', function() {

    var server      = require('../env').server(),
        multisite   = require('../env').multisite()

    before(function () {
        require('../login-as').admin(this)
    })

    var hasJetpack = false
    it('should open plugins page', function() {
        var pluginsUrl = multisite
            ? '/wp-admin/network/plugins.php'
            : '/wp-admin/plugins.php'

        casper.thenOpen(server + pluginsUrl, function () {
            expect(/Plugins/).to.matchTitle
            'Wp-testing'.should.be.textInDOM
            hasJetpack = this.evaluate(function() {
                return window['jetpack-by-wordpress-com'] != undefined
            })
        })
    })

    it('should activate jetpack plugin if it exists', function() {
        if (!hasJetpack) {
            this.skip()
            return
        }

        casper.then(function () {
            this.clickLabel('Activate', '*[@id="jetpack-by-wordpress-com"]/*//a')
        })

        casper.waitForUrl(/activate/, function() {
            '#toplevel_page_jetpack'.should.be.inDOM
        })

        casper.thenOpen(server + '/wp-admin/admin.php?page=jetpack_modules', function () {
            expect(/Jetpack/).to.matchTitle
            this.click('.manage-left input.checkall')
            this.evaluate(function() {
                jQuery('#bulk-action-selector-top').val('bulk-activate')
            })
            this.click('#doaction')
        })

        casper.waitForSelector('.jetpack-module.active', function() {
            'Fatal'.should.not.be.textInDOM
        }, null, 90000)

        casper.thenOpen(server + '/wp-admin/plugins.php', function () {
            expect(/Plugins/).to.matchTitle
            'Wp-testing'.should.be.textInDOM
        })
    })

    it('should activate main plugin and others', function() {
        casper.then(function () {
            this.click('#cb input')
            '#wpbody-content .wrap form'.should.be.inDOM
            this.evaluate(function() {
                jQuery('.wrap form select:first').val('activate-selected')
            })
            this.click('#doaction')
        })

        casper.waitForUrl(/activate/, function() {
            'Fatal'.should.not.be.textInDOM
            '#wp-testing .deactivate a,[data-slug=wp-testing] .deactivate a'.should.be.inDOM
        }, null, 90000)
    })
})
