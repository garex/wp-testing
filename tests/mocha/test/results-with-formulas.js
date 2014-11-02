describe('Results with formulas', function() {

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

    it('should be added to test and saved (even empty)', function() {
    })

    it('should be saved when formulas is good', function() {
    })

    it('should error when formulas is bad', function() {
    })
})
