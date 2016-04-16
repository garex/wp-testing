describe('Plugin deactivation', function() {

    var env         = require('../env'),
        multisite   = env.multisite(),
        server      = multisite ? env.multiServer() : env.server()

    before(function () {
        require('../login-as').admin(this, false, server)
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
           this.click('#wp-testing .deactivate a, [data-slug=wp-testing] .deactivate a')
       })

       casper.then(function() {
           '#wp-testing .activate a, [data-slug=wp-testing] .activate a'.should.be.inDOM
           '#wp-testing .delete a, [data-slug=wp-testing] .delete a'.should.be.inDOM
       })
    })

    it('should be deleted', function() {
       casper.then(function() {
           this.click('#wp-testing .delete a, [data-slug=wp-testing] .delete a')
       })

       casper.waitForUrl(/delete/, function() {
           this.click('#submit')
       })

       casper.then(function() {
           '#wp-testing, [data-slug=wp-testing]'.should.not.be.inDOM
       })
    })

})
