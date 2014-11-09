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

            this.fillSelectors('form#post', {
                '#title': 'Test With Results',
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
                '#wpt_score_value_2_0': '5'
            }, true)
        })
    })

    it('should be saved when formulas is good', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source=">"]')
            this.sendKeys('#wpt_formula_source_0', '10');
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM
            'wpt_formula_source_0.value'.should.evaluate.to.be.equal('scale-lie > 10')
        })
    })

    it('should error when formulas is bad', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source="<="]')
            this.sendKeys('#wpt_formula_source_1', 'nothing');
            this.click('#publish')
        })

        casper.waitForUrl(/post/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test data not saved'.should.be.textInDOM
            'Formula for Melancholic has error'.should.be.textInDOM
            this.clickLabel('« Back')
        })
    })
})
