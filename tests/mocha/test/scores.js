describe('Scores', function() {

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

    it('should be added in simple test', function() {
    })

    it('should allow to save only numbers', function() {
    })

    it('should be empties in case of zeros', function() {
    })

    it('should be added in test with many scales and answers', function() {
    })

    it('should have total sum by each scale', function() {
    })

    it('should be saved in test in case of scale toggle', function() {
    })
})
