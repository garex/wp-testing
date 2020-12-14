describe('Admin', function() {

    var server      = require('../env').server(),
        multisite   = require('../env').multisite()

    before(function () {
        require('../login-as').admin(this)
    })

    describe('Disable visual editing', function() {
        it('should open own profile', function () {
            casper.thenOpen(server + '/wp-admin/profile.php')
            casper.waitForUrl(/profile.php/)
        })

        it('disable visual checkbox', function () {
            casper.then(function () {
                this.evaluate(function() {
                    jQuery('#rich_editing').attr('checked', true)
                })
            })
        })

        it('should submit form', function() {
            casper.then(function() {
                this.evaluate(function() {
                    jQuery('#submit').click()
                })
            })

            casper.waitForUrl(/updated/)
        })
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
                    $('#noconfirmation').attr('checked', true)
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
                if (multisite) {
                    'User has been added to your site.'.should.be.textInDOM
                } else {
                    'user@wpti.dev'.should.be.textInDOM
                }
            })
        })

        if (!multisite) {
            return
        }

        it('should change user password to known one', function() {
            casper.thenOpen(server + '/wp-admin/user-edit.php?user_id=2', function() {
                this.evaluate(function() {
                    $=jQuery
                    $('#pass1').val('user')
                    $('#pass2').val('user')
                    $('#submit').click()
                })
            })

            casper.waitForUrl(/updated/, function() {
                'User updated.'.should.be.textInDOM
            })
        })
    })
})
