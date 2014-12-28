describe('Questions', function() {

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

    it('should be added to new test', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.fillSelectors('form#post', {
                '#title'                : 'To Be or Not to Be?',
                '#wpt_question_title_0' : 'To Be?',
                '#wpt_question_title_5' : 'Not to Be?'
            })
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_question_title_0.value'.should.evaluate.to.be.equal('To Be?')
            'wpt_question_title_1.value'.should.evaluate.to.be.equal('Not to Be?')
            'wpt_question_title_2.value'.should.evaluate.to.be.equal('')
        })
    })

    it('should be removed and updated in test', function() {
        casper.then(function() {
            this.clickLabel('All Tests', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.clickLabel('To Be or Not to Be?', 'a')
        })

        casper.then(function() {
            this.clickLabel(' Lie', 'label')

            this.fillSelectors('form#post', {
                '#wpt_question_title_0' : '',
                '#wpt_question_title_1' : 'Not to Be???',
                '#wpt_question_title_2' : 'But Why?'
            })
            this.fill('form#post', {}, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_question_title_0.value'.should.evaluate.to.be.equal('Not to Be???')
            'wpt_question_title_1.value'.should.evaluate.to.be.equal('But Why?')
            'wpt_question_title_2.value'.should.evaluate.to.be.equal('')
        })
    })

    it('should be added from quick fill', function() {
        casper.then(function() {
            this.clickLabel('All Tests', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.clickLabel('To Be or Not to Be?', 'a')
        })

        casper.then(function() {
            this.clickLabel('Quick Fill From Text', 'a')
            this.fillSelectors('form#post', {
                '#wpt_quick_fill_questions textarea': '1. Cool. \n2. "Quick"\n3. Question\n'
            })
            this.click('#wpt_quick_fill_questions .button')
            this.fill('form#post', {}, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_question_title_2.value'.should.evaluate.to.be.equal('Cool.')
            'wpt_question_title_3.value'.should.evaluate.to.be.equal('"Quick"')
            'wpt_question_title_4.value'.should.evaluate.to.be.equal('Question')
            'wpt_question_title_5.value'.should.evaluate.to.be.equal('')
        })
    })

    it('should be then shown in test', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            'Fatal'.should.not.be.textInDOM
            '"Quick"'.should.be.textInDOM
            '.wpt_test.fill_form'.should.be.inDOM
            'document.querySelectorAll(".wpt_test.fill_form .question").length'.should.evaluate.to.equal(5)
        })
    })

    it('should be in non-final test', function() {
        casper.then(function() {
            'Test is under construction'.should.be.textInDOM
            '#wpt-test-form input[type=submit]'.should.not.be.inDOM
        })
    })
})
