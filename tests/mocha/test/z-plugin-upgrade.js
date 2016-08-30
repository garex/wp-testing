describe('Plugin upgrade', function() {
    var isUpgrade = (require('system').env.WP_UPGRADE == 1)
    if (!isUpgrade) {
        it.skip('skipped')
        return
    }

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    it('should have plugin for upgrade', function() {
       casper.thenOpen(server + '/wp-admin/plugins.php?plugin_status=upgrade', function () {
           expect(/Plugins/).to.matchTitle
           'There is a new version of Hello Dolly'.should.be.textInDOM
       })
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

        casper.waitForSelector('.updated-message.notice-success')
    })

    it('should update site title through DB', function() {
        casper.thenOpen(server + '/', function() {
            'Upgraded from plugin'.should.be.textInDOM
        })
    })
})
