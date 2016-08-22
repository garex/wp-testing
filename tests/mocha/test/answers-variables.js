describe('Answers variables', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    var testEditUrl =
        testViewUrl = ''

    it('should show empty test as under construction initially', function() {
        casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
            this.clickLabel('Sorted Answers', '*[@id="posts-filter"]/*//a')
        })

        casper.waitForUrl(/edit/, function() {
            testEditUrl = this.getCurrentUrl()
            this.evaluate(function() {
                document.location = jQuery('#post-preview').attr('href')
            })
        })

        casper.waitForUrl(/answers/, function() {
            'Test is under construction'.should.be.textInDOM
            'form.wpt_test_form input[type=submit]'.should.not.be.inDOM
            testViewUrl = this.getCurrentUrl()
        })
    })

    it('should link to test one  result', function() {
        casper.thenOpen(testEditUrl, function() {
            this.clickLabel(' Choleric', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should add to test question-answer formula', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper button.question-answer')
            this.click('#publish')
        })
    })

    it('should show test as final then', function() {
        casper.open(testViewUrl, function() {
            'Test is under construction'.should.not.be.textInDOM
            'form.wpt_test_form input[type=submit]'.should.be.inDOM
        })
    })

    it('should show first result after choosing first question and answer', function() {
        casper.then(function() {
            this.clickLabel('Yes', '*[starts-with(@id, "wpt-test-form")]/*[1]/*//label')
            this.fill('form.wpt_test_form', {}, true)
        }).waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Choleric'.should.be.textInDOM
        })
    })

})
