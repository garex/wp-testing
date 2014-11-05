describe('Answers and Scales', function() {

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

    it('should be added to new test without questions', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle
            'jQuery("#wpt_answer-all input:checked").length'.should.evaluate.to.equal(0)
            'jQuery("#wpt_scale-all input:checked").length'.should.evaluate.to.equal(0)

            this.fill('form#post', {
                'post_title': 'Test With Answers and Scales Without Questions'
            })
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("#wpt_answer-all input:checked").length'.should.evaluate.to.equal(1)
            'jQuery("#wpt_scale-all input:checked").length'.should.evaluate.to.equal(1)
        })
    })

    it('should be added to new test with questions', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.fill('form#post', {
                'post_title': 'Test With Answers, Scales and Questions',
                'wp_testing_model_questions::question_title[0]': '5 + 5 is 10?',
                'wp_testing_model_questions::question_title[1]': '6 + 6 is 10?'
            })
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("#wpt_edit_questions .wpt_scale input[type=text]").attr("title")'.should.evaluate.to.equal('Lie, Yes')
        })
    })

    it('should be visible on test page', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            'Fatal'.should.not.be.textInDOM
            '#wpt-test-form .answer label'.should.be.inDOM
            '#wpt-test-form .answer label'.should.contain.text('Yes')
        })
    })

    it('should be removed from existing test', function() {
        casper.then(function() {
            this.clickLabel('Edit Test', 'a')
        })

        casper.then(function() {
            this.clickLabel(' Yes', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_questions .wpt_scale input[type=text]'.should.not.be.inDOM
        })
    })

    it('should then gone from existing test page', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            'Fatal'.should.not.be.textInDOM
            '#wpt-test-form .answer label'.should.not.be.inDOM
        })
    })
})
