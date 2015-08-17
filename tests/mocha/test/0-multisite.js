if (!require('../env').multisite()) {
    return
}

describe('Multisite', function() {

    var server          = require('../env').server(),
        anotherServer   = require('../env').anotherServer()

    before(function () {
        require('../login-as').admin(this)
    })

    it('should open network page', function() {
        casper.thenOpen(server + '/wp-admin/network.php', function () {
            expect(/Network/).to.matchTitle
        })
    })

    it('should setup network as sub-domains', function() {
        casper.then(function() {
            this.fill('form', {
                'subdomain_install': '1'
            }, true)

            this.waitForText('Enabling the Network')
        })
    })

    it('should see My Sites after relogin', function() {
        require('../login-as').admin(this)

        casper.then(function() {
            'My Sites'.should.be.textInDOM
        })
    })

    it('should add new site in network', function() {
        casper.thenOpen(server + '/wp-admin/network/site-new.php', function () {
            'Add New Site'.should.be.textInDOM

            this.fill('form', {
                'blog[domain]'  : 'another',
                'blog[title]'   : 'another',
                'blog[email]'   : 'wpti@wpti.dev'
            }, true)

            this.waitForUrl(/added/)
        })
    })

    it('should open another site plugins', function() {
        casper.thenOpen(anotherServer + '/wp-admin/plugins.php', function () {
            'Plugins'.should.be.textInDOM
            'Wp-testing'.should.be.textInDOM
        })
    })

    it('should activate main plugin on other site', function() {
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
            '#wp-testing .deactivate a'.should.be.inDOM
        }, null, 60000)
    })

    it('should show demo test on other site', function() {
        casper.thenOpen(anotherServer, function () {
            'The Eysenck Personality Inventory'.should.be.textInDOM
        })
    })
})
