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

        casper.waitForUrl(/test.+results/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test With Results'.should.be.inTitle
            '#wpt-test-form input[type=submit]'.should.be.inDOM
        })
    })

    it('should block "Get Test Results" button until all answers selected', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')

            this.clickLabel('Yezzzzzzz!', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[@id="wpt-test-form"]/*[2]/*//label')
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')

            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
        })
    })

    it('should show results with scales on submit', function() {
        casper.then(function() {
            this.clickLabel('Yezzzzzzz!', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[@id="wpt-test-form"]/*[2]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+results/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'Melancholic'.should.not.be.textInDOM
            'Lie'.should.be.textInDOM
            '15 out of 15'.should.be.textInDOM
        })
    })

    it('should be same after answers migrations', function() {
        casper.open('http://wpti.dev/?wpt_test=eysencks-personality-inventory-epi-extroversionintroversion').waitForUrl(/test.+eysencks/, function() {
            'Fatal'.should.not.be.textInDOM
            'Eysenck'.should.be.inTitle

            for (var i = 1, iMax = 57; i <= iMax; i++) {
                this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[' + i + ']/*//label')
                this.clickLabel('No',  '*[@id="wpt-test-form"]/*[' + i + ']/*//label')
            }
            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+eysencks/, function() {
            'Fatal'         .should.not.be.textInDOM
            'Sanguine'      .should.not.be.textInDOM
            'Choleric'      .should.not.be.textInDOM
            'Melancholic'   .should.not.be.textInDOM

            'Results'       .should.be.textInDOM
            'Phlegmatic'    .should.be.textInDOM
            '9 out of 24'   .should.be.textInDOM
            '0 out of 24'   .should.be.textInDOM
            '6 out of 9'    .should.be.textInDOM
        })
    })
})
