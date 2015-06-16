describe('Plugin upgrade', function() {
    var isUpgrade = (require('system').env.WP_UPGRADE == 1)
    if (!isUpgrade) {
        it.skip('skipped')
        return
    }

    before(function () {
        require('../login-as').admin(this)
    })

    it('should have plugin for upgrade', function() {
       casper.thenOpen('http://wpti.dev/wp-admin/plugins.php?plugin_status=upgrade', function () {
           expect(/Plugins/).to.matchTitle
           'There is a new version of Hello Dolly'.should.be.textInDOM
       })
    })

    it('should activate plugin before update', function() {
        casper.then(function() {
            this.clickLabel('Activate')
        })

        casper.waitForText(/Deactivate/)
    })

    it('should select plugin for update', function() {
        casper.then(function() {
            this.evaluate(function() {
                jQuery('select[name=action]')
                    .find('option:contains("Update")')
                        .attr('selected', 'selected')
            })

            this.click('#cb input')
        })
    })

    it('should update plugin', function() {
        casper.then(function() {
            this.click('#doaction')
        })

        casper.waitForUrl(/upgrade/)
    })

    it('should update site title through DB', function() {
        casper.withFrame(0, function() {
            this.waitForText(/All updates have been completed/, function() {
                this.thenOpen('http://wpti.dev/', function() {
                    'Upgraded from plugin'.should.be.textInDOM
                })
            })
        })
    })
})
