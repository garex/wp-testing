describe('Results with formulas', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
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
            this.click('.misc-pub-wpt-test-page-reset-answers-on-back input[type=checkbox]') // Reset answers on back
            this.click('.misc-pub-wpt-result-page-sort-scales-by-score input[type=checkbox]') // Sort scales by score
            this.clickLabel(' Yes',         'label')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.clickLabel(' Lie',         'label')
            this.clickLabel(' Choleric',    'label')
            this.clickLabel(' Melancholic', 'label')
            this.click('#save-post')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_1': '5.1',
                '#wpt_score_value_1_1': '4.95',
                '#wpt_score_value_2_1': '5',
                '#wpt_answer_title_0_0': 'Yezzzzzzz!',
                '#wpt_answer_title_1_0': 'I said yes. I confirm it.'
            })

            this.click('#save-post')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should show results page even in preview mode', function() {
        var currentUrl = casper.getCurrentUrl()

        casper.evaluate(function() {
            document.location = jQuery('#post-preview').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            this.clickLabel('Yezzzzzzz!',                '*[starts-with(@id, "wpt-test-form")]/*[1]/*//label')
            this.clickLabel('I said yes. I confirm it.', '*[starts-with(@id, "wpt-test-form")]/*[2]/*//label')
            this.clickLabel('Yes',                       '*[starts-with(@id, "wpt-test-form")]/*[3]/*//label')
            this.fill('form.wpt_test_form', {}, true)
        })

        casper.waitForUrl(/[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
        })

        casper.thenOpen(currentUrl)
    })

    it('should add formulas by buttons', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source=">"]')
            this.sendKeys('#wpt_formula_source_0', '14.999 "nothing"')
        })

        casper.then(function() {
            'wpt_formula_source_0.value'.should.evaluate.to.be.equal('scale-lie > 14.999 "nothing"')
        })
    })

    it('should replace selection for parentheses', function() {
        casper.evaluate(function() {
            wpt_formula_source_0.select()
        })

        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="( {selection} )"]')
        })

        casper.then(function() {
            'wpt_formula_source_0.value'.should.evaluate.to.be.equal('( scale-lie > 14.999 "nothing" )')
        })
    })

    it('should be saved when formulas is good', function() {
        casper.then(function() {
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM
            'wpt_formula_source_0.value'.should.evaluate.to.be.equal('( scale-lie > 14.999 "nothing" )')
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
