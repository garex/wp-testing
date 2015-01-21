describe('Results with formulas', function() {

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

    it('should be added to test and saved (even empty)', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.evaluate(function() {
                jQuery('#edButtonHTML,#content-html').addClass('__text_tab_here')
            })
            this.click('.__text_tab_here')

            this.fillSelectors('form#post', {
                '#title': 'Test Containing Results',
                '#content': 'London is the capital of great britan',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?',
                '#wpt_question_title_2': 'Question 3?'
            })
            this.clickLabel(' Yes',         'label')
            this.clickLabel(' Lie',         'label')
            this.clickLabel(' Choleric',    'label')
            this.clickLabel(' Melancholic', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '5',
                '#wpt_score_value_1_0': '5',
                '#wpt_score_value_2_0': '5',
                '#wpt_answer_title_0_0': 'Yezzzzzzz!',
                '#wpt_answer_title_1_0': 'I said yes. I confirm it.'
            }, true)
        })
    })

    it('should be saved when formulas is good', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source=">"]')
            this.sendKeys('#wpt_formula_source_0', '10 "nothing"')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM
            'wpt_formula_source_0.value'.should.evaluate.to.be.equal('scale-lie > 10 "nothing"')
        })
    })

    it('should error when formulas is bad', function() {
        casper.then(function() {
            this.click('#wpt_formula_source_1')
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source="<="]')
            this.sendKeys('#wpt_formula_source_1', '"nothing"')
            this.click('#publish')
        })

        casper.waitForUrl(/post/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test data not saved'.should.be.textInDOM
            'Formula for Melancholic has error'.should.be.textInDOM
            this.clickLabel('« Back')
        })
    })

    it('should be saved when formulas contains non-english slugs that should be unencoded', function() {
        casper.then(function() {
            this.sendKeys('#wpt_formula_source_1', '', {reset: true})
            this.click('#wpt_scale-add-toggle')
            this.sendKeys('#newwpt_scale', 'свобода')
            this.click('#wpt_scale-add-submit')
            this.waitForSelectorTextChange('#wpt_scalechecklist', function() {
                this.click('#publish')
            })
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            this.click('#wpt_formula_source_1')
            this.click('.wpt_formulas_helper input[data-source="свобода"]')
            this.click('.wpt_formulas_helper input[data-source=">"]')
            this.sendKeys('#wpt_formula_source_1', '0')
            'wpt_formula_source_1.value'.should.evaluate.to.be.equal('свобода > 0')
            this.click('#publish')

        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test data not saved'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM
            'wpt_formula_source_1.value'.should.evaluate.to.be.equal('свобода > 0')
        })
    })
})
