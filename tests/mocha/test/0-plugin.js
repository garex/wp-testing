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
        }, null, 10000)
    })

})
