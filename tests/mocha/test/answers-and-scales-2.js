describe('Answers2 and Scales2', function() {

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
            'wpt_score_value_0_0.value'.should.evaluate.to.be.equal('3')
            'wpt_score_value_1_0.value'.should.evaluate.to.be.equal('4')
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
                '#wpt-add-individual-answers-to-question-0': '1. Some indanswer\n2. "Anoter" answer!\n\n'
            }, true)
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.not.be.textInDOM
            'wpt_answer_title_0_0.value'.should.evaluate.to.be.equal('Some indanswer')
            'wpt_answer_title_0_1.value'.should.evaluate.to.be.equal('"Anoter" answer!')
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
            'wpt_answer_title_0_0.value'.should.evaluate.to.be.equal('"Anoter" answer!')
            '#wpt_answer_title_0_1.value'.should.not.be.inDOM
        })
    })

    it('should be able to be sorted by user', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.fillSelectors('form#post', {
                '#title': 'Test With Answers Sorted',
                '#wpt_question_title_0': 'Question 1',
            })
            this.clickLabel(' No', 'label')
            this.clickLabel(' Yes', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_answer_title_0_0.title'.should.evaluate.to.be.equal('No')
            'wpt_answer_title_0_1.title'.should.evaluate.to.be.equal('Yes')
            this.evaluate(function() {
                document.location = jQuery('#view-post-btn a').attr('href')
            })
        })

        casper.waitForUrl(/answers-sorted/, function() {
            'Fatal'.should.not.be.textInDOM
            'jQuery(".question .answer:first").text()'.should.evaluate.to.be.equal('No')
        })

        casper.back().viewport(1280, 720).then(function() {
            '#message'.should.be.inDOM

            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Yes', 'label')

            // Manually move Yes before No
            var yesId = '#' + this.evaluate(function() {
                return jQuery('#wpt_answer-all label').parent().next().attr('id')
            })
            this.mouse.down(yesId)
            var b = this.getElementBounds(yesId)
            for (var i=0; i < b.height + 5; i++) {
                this.mouse.move(b.left, b.top - i)
            }
            this.mouse.up(b.left, b.top)

            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_answer_title_0_0.title'.should.evaluate.to.be.equal('Yes')
            'wpt_answer_title_0_1.title'.should.evaluate.to.be.equal('No')
        })
    })
})
