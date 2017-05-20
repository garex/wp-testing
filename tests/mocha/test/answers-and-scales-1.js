describe('Answers1 and Scales1', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
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

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
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
            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
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

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_question_title_0.value'.should.evaluate.to.be.equal('`5 + 5 is "10?')
            'wpt_question_title_1.value'.should.evaluate.to.be.equal('6 + 6 is \'10?')

            this.clickLabel(' Extraversion/Introversion', 'label')
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '1'
            })
            this.click('#publish')
       })

       casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
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
       casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
           'Fatal'.should.not.be.textInDOM
           '#message'.should.be.inDOM
           '#wpt_score_value_0_0'.should.be.inDOM
           '#wpt_score_value_0_1'.should.be.inDOM
      })
    })

    it('should sort scales in the order of adding', function() {
        casper.then(function() {
            '.wpt_scores .wpt_scales .wpt_title:nth-of-type(2n - 1)'.should.contain.text('Lie')
            '.wpt_scores .wpt_scales .wpt_title:nth-of-type(2n + 0)'.should.contain.text('Extraversion')
        })
    })

    it('should be visible on test page', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a,#post-preview').attr('href')
        })

        casper.waitForUrl(/questions/, function() {
            'Fatal'.should.not.be.textInDOM
            '`5 + 5 is “10?'.should.be.textInDOM
            '6 + 6 is ’10?'.should.be.textInDOM
            'form.wpt_test_form .answer label'.should.be.inDOM
            'form.wpt_test_form .answer label'.should.contain.text('Yes')
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

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_questions .wpt_scale input'.should.not.be.inDOM
        })
    })

    it('should then gone from existing test page', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a,#post-preview').attr('href')
        })

        casper.waitForUrl(/questions/, function() {
            'Fatal'.should.not.be.textInDOM
            'form.wpt_test_form .answer label'.should.not.be.inDOM
        })
    })
})
