function wptDescribePassings(isPermalinks) {
describe('Passings' + (isPermalinks ? ' with permalinks' : ''), function() {

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

    it('should setup permalinks', function() {
        casper.thenOpen('http://wpti.dev/wp-admin/options-permalink.php')

        casper.waitForUrl(/options/).then(function() {
            'Permalink Settings'.should.be.textInDOM
            if (isPermalinks) {
                this.click('#permalink_structure')
                this.sendKeys('#permalink_structure', '/%postname%/');
            } else {
                this.clickLabel(' Default', 'label')
            }
            this.click('#submit')
        })

        // Fix for WP 3.2 flush rules
        casper.waitForUrl(/options/).then(function () {
            this.click('#submit')
        })

        casper.waitForUrl(/options/).thenOpen('http://wpti.dev/wp-login.php?action=logout', function() {
            this.clickLabel('log out', 'a')
        })

        casper.waitForUrl(/loggedout/)
    })

    it('should error on non-good passing slug', function() {
        var url = isPermalinks
            ? 'http://wpti.dev/test/test-containing-results/wtf/'
            : 'http://wpti.dev/?wpt_test=test-containing-results&wpt_passing_slug=wtf';

        casper.open(url).waitForUrl(/wtf/, function() {
            'Fatal'.should.not.be.textInDOM
            this.getTitle().should.match(isPermalinks
                ? /^Page not found/
                : /Test result not found/
            )
        })
    })

    it('should open test for visitor', function() {
        casper.open('http://wpti.dev/')

        casper.then(function() {
            '.wp-testing.shortcode.list'.should.be.inDOM
            '.wp-testing.shortcode.list li'.should.contain.text('Test Containing Results')
            this.clickLabel('Test Containing Results')
        })

        casper.waitForUrl(/test.+results/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test Containing Results'.should.be.inTitle
            '#wpt-test-form input[type=submit]'.should.be.inDOM
        })
    })

    var uuidCookie = null;
    it('should block "Get Test Results" button until all answers selected', function() {
        casper.then(function() {

            this.wait(1000, function() {
                uuidCookie = this.evaluate(function() {
                    return document.cookie.match(/device_uuid=[a-z0-9\-]+/)
                })

                expect(uuidCookie).should.not.be.null
            })

            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')

            this.clickLabel('Yezzzzzzz!', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[@id="wpt-test-form"]/*[2]/*//label')
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')

            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
        })
    })

    var resultUrl = null;
    it('should show results with scales on submit', function() {
        casper.then(function() {
            'London is the capital of great britan'.should.be.textInDOM
            this.clickLabel('Yezzzzzzz!', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[@id="wpt-test-form"]/*[2]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+results.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'Melancholic'.should.not.be.textInDOM
            'Lie'.should.be.textInDOM
            '15 out of 15'.should.be.textInDOM
            resultUrl = this.getCurrentUrl();
        })
    })

    it('should reset answers on back if this option enabled', function() {
        casper.back().then(function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
        })
    })

    it('should give same results on back-n-submit with new passing url', function() {
        casper.then(function() {
            this.clickLabel('Yezzzzzzz!', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[@id="wpt-test-form"]/*[2]/*//label')
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[3]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+results.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'Melancholic'.should.not.be.textInDOM
            'Lie'.should.be.textInDOM
            '15 out of 15'.should.be.textInDOM

            this.getCurrentUrl().should.not.be.equal(resultUrl)
            resultUrl = this.getCurrentUrl();
        })
    })

    it('should have same device_uuid in cookie', function() {
        casper.wait(1000, function() {
            uuidCookie2 = this.evaluate(function() {
                return document.cookie.match(/device_uuid=[a-z0-9\-]+/)
            })

            expect(uuidCookie2).should.not.be.null
            uuidCookie2[0].should.be.equal(uuidCookie[0])
        })
    })

    it('should show result by existing url', function() {
        casper.open(resultUrl).waitForUrl(/test.+results.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'Melancholic'.should.not.be.textInDOM
            'Lie'.should.be.textInDOM
            '15 out of 15'.should.be.textInDOM
        })
    })

    it('should show scales and not test description for new tests by default', function() {
        casper.open(resultUrl).waitForUrl(/test.+results.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
            'London is the capital of great britan'.should.not.be.textInDOM
        })
    })

    it('should show customized button title', function() {
        casper.open('http://wpti.dev/')

        casper.then(function() {
            this.clickLabel('Are You Hot or Not?!')
        })

        casper.waitForUrl(/test/, function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('value', 'Gimme Gimme')
        })
    })

    it('should not show scales and test description when they are disabled', function() {
        casper.then(function() {
            'Allow others to rate the vacuum on the Earth'.should.be.textInDOM
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Lie'.should.not.be.textInDOM
            'Allow others to rate the vacuum on the Earth'.should.not.be.textInDOM
        })
    })

    it('should be same after answers migrations', function() {
        var url = isPermalinks
                ? 'http://wpti.dev/test/eysencks-personality-inventory-epi-extroversionintroversion/'
                : 'http://wpti.dev/?wpt_test=eysencks-personality-inventory-epi-extroversionintroversion';
        casper.open(url).waitForUrl(/test.+eysencks/, function() {
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

            resultUrl = this.getCurrentUrl();
        })
    })

    it('should be blocked in non-final test on post', function() {
        casper.open('http://wpti.dev/').then(function() {
            this.clickLabel('To Be or Not to Be?!')
        })

        casper.waitForUrl(/not/, function() {
            'Fatal'.should.not.be.textInDOM
            'To Be'.should.be.inTitle
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.then(function() {
            '#wpt-test-form'.should.not.be.inDOM
            'Test is under construction'.should.be.textInDOM
        })
    })

    it('should show scales and test description for existing tests', function() {
        casper.open(resultUrl).waitForUrl(/test.+eysencks.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Extraversion/Introversion'.should.be.textInDOM
            'The Eysenck Personality Inventory (EPI) measures two pervasive'.should.be.textInDOM
        })
    })

})
}

wptDescribePassings(false);
wptDescribePassings(true);
