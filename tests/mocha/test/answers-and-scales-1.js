describe('Answers1 and Scales1', function() {

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

            this.fillSelectors('form#post', {
                '#title': 'Test With Answers and Scales Without Questions'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
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
            this.fillSelectors('form#post', {
                '#title': 'Test With Answers, Scales and Questions',
                '#wpt_question_title_0': '`5 + 5 is "10?',
                '#wpt_question_title_1': '6 + 6 is \'10?'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("#wpt_edit_questions .wpt_scale input[type=text]").attr("title")'.should.evaluate.to.equal('Lie, Yes')
            'wpt_question_title_0.value'.should.evaluate.to.be.equal('`5 + 5 is "10?')
            'wpt_question_title_1.value'.should.evaluate.to.be.equal('6 + 6 is \'10?')

            this.clickLabel(' Extraversion/Introversion', 'label')
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '1'
            })
            this.click('#publish')
       })

       casper.waitForUrl(/message/, function() {
           'Fatal'.should.not.be.textInDOM
           '#message'.should.be.inDOM
      })
    })

    it('should not disapear on repeated save', function() {
       casper.then(function() {
           '#wpt_score_value_0_0'.should.be.inDOM
           '#wpt_score_value_0_1'.should.be.inDOM
            this.click('#publish')
       })
       casper.waitForUrl(/message/, function() {
           'Fatal'.should.not.be.textInDOM
           '#message'.should.be.inDOM
           '#wpt_score_value_0_0'.should.be.inDOM
           '#wpt_score_value_0_1'.should.be.inDOM
      })
    })

    it('should sort scales in the order of adding', function() {
        casper.then(function() {
            'wpt_score_value_0_0.title'.should.evaluate.to.be.equal('Lie, Yes')
            'wpt_score_value_0_1.title'.should.evaluate.to.be.equal('Extraversion/Introversion, Yes')
        })
    })

    it('should be visible on test page', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            'Fatal'.should.not.be.textInDOM
            '`5 + 5 is "10?'.should.be.textInDOM
            '6 + 6 is \'10?'.should.be.textInDOM
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
