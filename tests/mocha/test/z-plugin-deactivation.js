describe('Plugin deactivation', function() {

    var server      = require('../env').server(),
        multisite   = require('../env').multisite()

    before(function () {
        require('../login-as').admin(this)
    })

    it('should be deactivated', function() {
       var pluginsUrl = multisite
           ? '/wp-admin/network/plugins.php'
           : '/wp-admin/plugins.php'

       casper.thenOpen(server + pluginsUrl, function () {
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
       if (multisite) {
           this.skip()
           return
       }

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
