describe('Scores', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    it('should be added in simple test', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Lie', 'label')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.fillSelectors('form#post', {
                '#title': 'Simple Test With Scores',
                '#wpt_question_title_0': 'Does tomato red?',
                '#wpt_question_title_1': 'Do you like tomatos?',
                '#wpt_question_title_2': 'Are you Pinokkio?'
            })
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ 0"]'.should.be.inDOM
            '#wpt_edit_formulas input[value="Extraversion/Introversion, ∑ 0"]'.should.be.inDOM
            this.clickLabel('Add Individual Answer')
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_1': '-1',
                '#wpt_score_value_0_0': '10',
                '#wpt_score_value_0_1': '-1',
                '#wpt_score_value_1_1': '5',
                '#wpt_score_value_2_1': '3',
                '#wpt_answer_title_1_0': 'Yeah!',
                '#wpt_answer_title_0_1': 'I am color blind!'
            }, true)
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_answer_title_0_1'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ 7, max 8"]'.should.be.inDOM
        })
    })

    it('should empty invalid values', function() {
        casper.then(function() {
            'wpt_score_value_2_1.value'.should.evaluate.to.be.equal('3')
            this.evaluate(function() {
                wpt_score_value_2_0.type = 'text'
            })
            this.fillSelectors('form#post', {
                '#wpt_score_value_2_1': 'bad value'
            }, true)
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'wpt_score_value_2_1.value'.should.not.evaluate.to.be.equal('3')
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should be empties in case of zeros', function() {
        casper.then(function() {
            'wpt_score_value_2_1.value'.should.evaluate.to.be.equal('')
        })
    })

    it('should have total sum by each scale', function() {
        casper.then(function() {
            '#wpt_edit_formulas input[value="Lie, ∑ 4, max 5"]'.should.be.inDOM
        })
    })

    it('should be saved in test in case of scale toggle', function() {
        casper.then(function() {
            this.clickLabel(' Lie', 'label')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value^="Lie"]'.should.not.be.inDOM
        })

        casper.then(function() {
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ 4, max 5"]'.should.be.inDOM
        })
    })

    it('should be filled from quick fill', function() {
        casper.then(function() {
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_1': '5'
            })
            this.evaluate(function() {
                jQuery('#wpt_score_value_0_1').trigger('input')
            })
            this.fillSelectors('form#post', {
                '.wpt_add_new_combination [ng-model="newScaleIndex"]'   : '0',
                '.wpt_add_new_combination [ng-model="newScore"]'        : '2',
                '.wpt_add_new_combination [ng-model="newAnswer"]'       : '1'
            })
            this.clickLabel('Add new combination', 'button')
            this.fillSelectors('form#post', {
                '#wpt_quick_fill_scores .entry_index_1 .questions input': '1, 2, 3, 4'
            })
            this.evaluate(function() {
                jQuery('#wpt_quick_fill_scores .entry_index_1 .questions input').trigger('input')
            })
        })
        casper.then(function() {
            'wpt_score_value_0_0.value'.should.evaluate.to.be.equal('2')
            'wpt_score_value_0_1.value'.should.evaluate.to.be.equal('5')
            'wpt_score_value_1_0.value'.should.evaluate.to.be.equal('2')
            'wpt_score_value_2_0.value'.should.evaluate.to.be.equal('2')
        })
        casper.then(function() {
            'jQuery("#wpt_quick_fill_scores .entry_index_1 .score").text()'.should.evaluate.to.be.equal('2'),
            'jQuery("#wpt_quick_fill_scores .entry_index_1 .questions input").val()'.should.evaluate.to.be.equal('1, 2, 3')
        })
    })
})
