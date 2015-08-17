describe('Admin', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    describe('Create default user in subscriber role', function() {

        it('should fill new user form', function() {
            casper.thenOpen(server + '/wp-admin/user-new.php', function() {
                this.evaluate(function() {
                    $=jQuery
                    $('#user_login').val('user')
                    $('#email').val('user@wpti.dev')
                    $('#pass1').data('pw', 'user').val('user')
                    $('#pass1-text').val('user')
                    $('#pass2').val('user')
                })
            })
        })

        it('should submit form and check that user added', function() {
            casper.then(function() {
                this.evaluate(function() {
                    $('#createuser').submit()
                })
            })

            casper.waitForUrl(/update/, function() {
                'user@wpti.dev'.should.be.textInDOM
            })
        })

    })
})
