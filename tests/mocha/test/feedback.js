describe('Feedback', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    describe('Rate us', function() {
        it('should have rate us link in tests page', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                'Rate us'.should.be.textInDOM
            })
        })

        it('should not have "thank you" text', function() {
            casper.then(function() {
                'Thank you'.should.not.be.textInDOM
            })
        })

        it('should open review page on rate us link', function() {
            casper.then(function() {
                this.clickLabel('Rate us')
            }).wait(500)
        })

        it('should show "thank you" text after reload', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                'Rate us'.should.not.be.textInDOM
                'Thank you'.should.be.textInDOM
            })
        })
    })
})
