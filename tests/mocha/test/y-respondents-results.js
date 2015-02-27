describe('Respondents results', function() {

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

    it('should be in admin area', function() {
        casper.then(function() {
            this.clickLabel('Respondents’ results', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.waitForUrl(/respondents/, function() {
            'Respondents'.should.be.inTitle
        })
    })

    it('should have items', function() {
        casper.then(function() {
            '0 items'.should.not.be.textInDom
        })
    })

    it('should have user agent column with values', function() {
        casper.then(function() {
            'Browser'.should.be.textInDom
            'PhantomJS'.should.be.textInDom
        })
    })

    it('should have tests titles', function() {
        casper.then(function() {
            'Test Containing Results'.should.be.textInDom
            'Are You Hot or Not?!'.should.be.textInDom
            'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'.should.be.textInDom
            'Simple Test With Scores'.should.not.be.textInDom
        })
    })

    it('should allow to view test results', function() {
        casper.then(function() {
            this.clickLabel('View', '*[contains(@class,"actions")]/a[text()="View"]')
        })

        casper.waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Results'.should.be.textInDom
            ' out of '.should.be.textInDom
        })
    })

})
