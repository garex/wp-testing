describe('Passings', function() {

    before(function () {
        casper.start('http://wpti.dev/')
    })

    it('should open test for visitor', function() {
        casper.then(function() {
            '.wp-testing.shortcode.list'.should.be.inDOM
            '.wp-testing.shortcode.list li'.should.contain.text('Test With Results')
            this.clickLabel('Test With Results')
        })

        casper.waitForUrl(/test-with-results/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test With Results'.should.be.inTitle
            '#wpt-test-form input[type=submit]'.should.be.inDOM
        })
    })

    it('should block "Get Test Results" button until all answers selected', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')

            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[2]/*//label')
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')

            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
        })
    })

    it('should show results with scales on submit', function() {
        casper.then(function() {
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[2]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test-with-results/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'Melancholic'.should.not.be.textInDOM
            'Lie'.should.be.textInDOM
            '15 out of 15'.should.be.textInDOM
        })
    })
})
