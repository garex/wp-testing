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
                '#wpt_question_title_0': '5 + 5 is 10?',
                '#wpt_question_title_1': '6 + 6 is 10?'
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

    it('should be synced with global answers on their check uncheck with scores saving', function() {
        casper.open('http://wpti.dev/wp-admin/').then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.fillSelectors('form#post', {
                '#title': 'Test With Answers and Scores synced',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' No', 'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("td.wpt_scale").length'.should.evaluate.to.equal(4)
        })

        casper.then(function() {
            this.clickLabel(' No', 'label')
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '1',
                '#wpt_score_value_1_0': '2',
                '#wpt_score_value_0_1': '3',
                '#wpt_score_value_1_1': '4',
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("td.wpt_scale").length'.should.evaluate.to.equal(2)
            'wpt_score_value_0_0.value'.should.evaluate.to.be.equal('1')
            'wpt_score_value_1_0.value'.should.evaluate.to.be.equal('2')
            'wpt_score_value_0_1.value'.should.evaluate.to.be.equal(null)
            'wpt_score_value_1_1.value'.should.evaluate.to.be.equal(null)
        })
    })

    it('should take individual answers into account when determining can edit scores', function() {
        casper.open('http://wpti.dev/wp-admin/').then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'No scores to edit'.should.not.be.textInDOM
            this.fillSelectors('form#post', {
                '#title': 'Test With Individual Answer With Scores',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.be.textInDOM
        })

        casper.then(function() {
            this.clickLabel(' Yes', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.not.be.textInDOM
        })
    })

    it('should add individual answers from text', function() {
        casper.then(function() {
            this.clickLabel(' Yes', 'label')
            this.clickLabel('Add Individual Answers')
            this.fillSelectors('form#post', {
                '#wpt-add-individual-answers-to-question-0': '1. Some indanswer\n2. Anoter answer!\n\n'
            }, true)
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.not.be.textInDOM
            'wpt_answer_title_0_0.value'.should.evaluate.to.be.equal('Some indanswer')
            'wpt_answer_title_0_1.value'.should.evaluate.to.be.equal('Anoter answer!')
            '#wpt_answer_title_0_2.value'.should.not.be.inDOM
        })
    })

    it('should remove individual answers by empty title', function() {
        casper.then(function() {
            this.fillSelectors('form#post', {
                '#wpt_answer_title_0_0': ''
            }, true)
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_answer_title_0_0.value'.should.evaluate.to.be.equal('Anoter answer!')
            '#wpt_answer_title_0_1.value'.should.not.be.inDOM
        })
    })
})
