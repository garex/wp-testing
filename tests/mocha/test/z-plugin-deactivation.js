describe('Plugin deactivation', function() {

    var env         = require('../env'),
        multisite   = env.multisite(),
        isDelete    = env.isDelete(),
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
        if (!isDelete) {
            this.skip()
        }

        casper.setFilter('page.confirm', function(msg) {
            return true
        });

       casper.then(function() {
           this.click('#wp-testing .delete a, [data-slug=wp-testing] .delete a')
       })

       var isClickThen = hasDeletedId = false
       casper.waitFor(function check() {
           isClickThen = this.evaluate(function() {
               return document.location.href.indexOf('delete') > -1
           })

           hasDeletedId = this.evaluate(function() {
               return document.getElementById('wp-testing-deleted') != null
           })

           return isClickThen || hasDeletedId
       }, function then() {
           isClickThen && this.click('#submit')
       }, null, 30000)

       casper.then(function() {
           if (isClickThen) {
               '#wp-testing, [data-slug=wp-testing]'.should.not.be.inDOM
           }
           if (hasDeletedId) {
               '#wp-testing-deleted'.should.be.inDOM
           }
       })
    })

})
