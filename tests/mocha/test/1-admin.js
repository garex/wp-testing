describe('Admin', function() {

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

    describe('Create default user in subscriber role', function() {

        it('should fill new user form', function() {
            casper.thenOpen('http://wpti.dev/wp-admin/user-new.php', function() {
                this.fill('form#createuser', {
                    'user_login' : 'user',
                    'email'      : 'user@wpti.dev',
                    'pass1'      : 'user',
                    'pass2'      : 'user'
                })
            })
        })

        it('should submit form and check that user added', function() {
            casper.then(function() {
                this.fill('form#createuser', {}, true)
            })

            casper.waitForUrl(/update/, function() {
                'user@wpti.dev'.should.be.textInDOM
            })
        })

    })
})
