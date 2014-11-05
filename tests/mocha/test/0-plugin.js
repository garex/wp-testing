describe('Plugin', function() {

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
           '.plugin-title'.should.contain.text('Wp-testing')
           '#wp-testing .activate a'.should.be.inDOM
           this.click('#wp-testing .activate a')
       })

       casper.then(function() {
           '#wp-testing .deactivate a'.should.be.inDOM
       })
    })

})
