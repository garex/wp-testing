if (!require('../env').multisite()) {
    return
}

require('../after-fail').screenshots()

describe('Multisite', function() {

    var server          = require('../env').multiServer(),
        serverBefore    = require('../env').anotherServer('before'),
        serverAfter     = require('../env').anotherServer('after')

    before(function () {
        require('../login-as').admin(this, false, server)
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
        require('../login-as').admin(this, false, server)

        casper.then(function() {
            'My Sites'.should.be.textInDOM
        })
    })

    it('should add site in network before activation', function() {
        casper.thenOpen(server + '/wp-admin/network/site-new.php', function () {
            'Add New Site'.should.be.textInDOM

            this.fill('form', {
                'blog[domain]'  : 'before',
                'blog[title]'   : 'before',
                'blog[email]'   : 'wpti@wpti.dev'
            }, true)

            this.waitForUrl(/added/)
        })
    })

    it('should open network plugins', function() {
        casper.thenOpen(server + '/wp-admin/network/plugins.php', function () {
            'Plugins'.should.be.textInDOM
            'Wp-testing'.should.be.textInDOM
        })
    })

    it('should activate plugin on network', function() {
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
        }, null, 120000)
    })

    it('should add site in network after activation', function() {
        casper.thenOpen(server + '/wp-admin/network/site-new.php', function () {
            'Add New Site'.should.be.textInDOM

            this.fill('form', {
                'blog[domain]'  : 'after',
                'blog[title]'   : 'after',
                'blog[email]'   : 'wpti@wpti.dev'
            }, true)

            this.waitForUrl(/added/, null, null, 60000)
        })
    })

    it('should show demo test on before activation site', function() {
        casper.thenOpen(serverBefore, function () {
            'The Eysenck Personality Inventory'.should.be.textInDOM
        })
    })

    it('should show demo test on after activation site', function() {
        casper.thenOpen(serverAfter, function () {
            'The Eysenck Personality Inventory'.should.be.textInDOM
        })
    })
})
