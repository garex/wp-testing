describe('Answers2 and Scales2', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    it('should be synced with global answers on their check uncheck with scores saving', function() {
        casper.open(server + '/wp-admin/').then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
            this.fillSelectors('form#post', {
                '#title': 'Test With Answers and Scores synced',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' No', 'label')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("td.wpt_score").length'.should.evaluate.to.equal(4)
        })

        casper.then(function() {
            this.clickLabel(' No', 'label')

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '1',
                '#wpt_score_value_1_0': '2',
                '#wpt_score_value_0_1': '3',
                '#wpt_score_value_1_1': '4',
            })
            this.evaluate(function() {
                jQuery('[ng-model="score.value"]').trigger('input')
            })
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'jQuery("td.wpt_score").length'.should.evaluate.to.equal(2)
            'wpt_score_value_0_0.value'.should.evaluate.to.be.equal('3')
            'wpt_score_value_1_0.value'.should.evaluate.to.be.equal('4')
            'typeof wpt_score_value_0_1'.should.evaluate.to.be.equal('undefined')
            'typeof wpt_score_value_1_1'.should.evaluate.to.be.equal('undefined')
        })
    })

    it('should take individual answers into account when determining can edit scores', function() {
        casper.open(server + '/wp-admin/').then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'No scores to edit'.should.be.textInDOM
            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
            this.fillSelectors('form#post', {
                '#title': 'Test With Individual Answer With Scores',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.click('.misc-pub-wpt-publish-on-home input[type=checkbox]')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.be.textInDOM
        })

        casper.then(function() {
            this.clickLabel(' Yes', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.not.be.textInDOM
        })
    })

    it('should add individual answers by button', function() {
        casper.then(function() {
            this.clickLabel(' Yes', 'label')
            this.clickLabel('Add Individual Answer')
            this.clickLabel('Add Individual Answer')
            this.fillSelectors('form#post', {
                '#wpt_answer_title_0_1': 'Some indanswer',
                '#wpt_answer_title_0_2': '"Anoter" answer!'
            })
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'No scores to edit'.should.not.be.textInDOM
            'wpt_answer_title_0_0.value'.should.evaluate.to.be.equal('Some indanswer')
            'wpt_answer_title_0_1.value'.should.evaluate.to.be.equal('"Anoter" answer!')
            '#wpt_answer_title_0_2.value'.should.not.be.inDOM
        })
    })

    it('should remove individual answers by button', function() {
        casper.then(function() {
            this.click('button[title="Remove Answer"]');
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
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
            this.click('#wpt_question_add');
            this.fillSelectors('form#post', {
                '#title': 'Sorted Answers',
                '#wpt_question_title_0': 'Question 1',
            })
            this.clickLabel(' No', 'label')
            this.clickLabel(' Yes', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_answer_title_0_0.title'.should.evaluate.to.be.equal('No')
            'wpt_answer_title_0_1.title'.should.evaluate.to.be.equal('Yes')
            this.evaluate(function() {
                document.location = jQuery('#view-post-btn a,#post-preview').attr('href')
            })
        })

        casper.waitForUrl(/sorted-answers/, function() {
            'Fatal'.should.not.be.textInDOM
            'jQuery(".question .answer:first").text().trim()'.should.evaluate.to.be.equal('No')
            this.clickLabel('Edit Test')
        })

        casper.waitForUrl(/edit/, function() {
            'Edit Test'.should.be.textInDOM
        })

        casper.viewport(1280, 3000).then(function() {
            // Manually move Yes before No
            var els = this.evaluate(function() {
                return {
                    no  : '#' + jQuery('#wpt_answer-all label:first').parent().attr('id') + ' label',
                    yes : '#' + jQuery('#wpt_answer-all label:first').parent().next().attr('id') + ' label'
                };
            })

            this.wait(10).mouse.down(els.no)

            this.wait(10).mouse.move(els.yes)
            this.wait(10).mouse.move(els.yes)
            this.wait(10).mouse.move(els.yes)
            this.wait(10).mouse.move('#wpt_answer-adder')
            this.wait(10).mouse.move(els.yes)
            this.wait(10).mouse.move('#wpt_answer-adder')
            this.wait(10).mouse.move(els.yes)
            this.wait(10).mouse.move('#wpt_answer-adder')

            this.wait(10).mouse.move('#wpt_answer-all .sortable-placeholder')
            this.wait(10).mouse.up('#wpt_answer-all .sortable-placeholder')
        })

        casper.then(function() {
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            'wpt_answer_title_0_0.title'.should.evaluate.to.be.equal('Yes')
            'wpt_answer_title_0_1.title'.should.evaluate.to.be.equal('No')
        })
    })
})
