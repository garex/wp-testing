describe('Plugin_activation', function() {

    before(function () {
        this.timeout(3600000)
        casper.start('http://wpti.dev/wp-admin/').thenOpen('http://wpti.dev/wp-login.php', {
            method: 'post',
            data  : {
                log: 'wpti',
                pwd: 'wpti'
            }
        })
    })

    it('should be activated', function() {
        casper.thenOpen('http://wpti.dev/wp-admin/plugins.php', function () {
            expect(/Plugins/).to.matchTitle
            'Wp-testing'.should.be.textInDOM
            this.click('#cb input')
            '#wpbody-content .wrap form'.should.be.inDOM
            this.evaluate(function() {
                jQuery('.wrap form select:first').val('activate-selected')
            })
            this.click('#doaction')
        })

        casper.waitForUrl(/activate/, function() {
            'Fatal'.should.not.be.textInDOM
            '#wp-testing .deactivate a'.should.be.inDOM

            var hasJetpack = this.evaluate(function() {
                return jQuery('#toplevel_page_jetpack').length > 0
            })

            if (hasJetpack) {
                activateJetpackModules()
            }
        }, null, 60000)
    })

    function activateJetpackModules() {
        casper.thenOpen('http://wpti.dev/wp-admin/admin.php?page=jetpack_modules', function () {
            expect(/Jetpack/).to.matchTitle
            this.click('.manage-left input.checkall')
            this.evaluate(function() {
                jQuery('#bulk-action-selector-top').val('bulk-activate')
            })
            this.click('#doaction')
        })

        casper.waitForSelector('.jetpack-module.active', function() {
            'Fatal'.should.not.be.textInDOM
        }, null, 60000)
    }

})
