describe('Plugin_deactivation', function() {

    before(function () {
        require('../login-as').admin(this)
    })

    it('should be deactivated', function() {
       casper.thenOpen('http://wpti.dev/wp-admin/plugins.php', function () {
           expect(/Plugins/).to.matchTitle
           '.plugin-title'.should.contain.text('Wp-testing')
       })

       casper.then(function() {
           this.click('#wp-testing .deactivate a')
       })

       casper.then(function() {
           '#wp-testing .activate a'.should.be.inDOM
           '#wp-testing .delete a'.should.be.inDOM
       })
    })

    it('should be deleted', function() {

       casper.then(function() {
           this.click('#wp-testing .delete a')
       })

       casper.then(function() {
           this.click('#submit')
       })

       casper.then(function() {
           '#wp-testing'.should.not.be.inDOM
       })
    })

})
