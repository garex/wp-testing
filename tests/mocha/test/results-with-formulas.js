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

            this.fill('form#post', {
                'post_title': 'Test With Results',
                'wp_testing_model_questions::question_title[0]': 'Question 1?',
                'wp_testing_model_questions::question_title[1]': 'Question 2?',
                'wp_testing_model_questions::question_title[2]': 'Question 3?'
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

            this.fill('form#post', {
                'wp_testing_model_questions::wp_testing_model_score::score_value[0][0]': '5',
                'wp_testing_model_questions::wp_testing_model_score::score_value[1][0]': '5',
                'wp_testing_model_questions::wp_testing_model_score::score_value[2][0]': '5'
            }, true)
        })
    })

    it('should be saved when formulas is good', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source=">"]')
            this.sendKeys('input[name="wp_testing_model_formulas::formula_source[0]"]', '10');
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas .wpt_result'.should.be.inDOM
            expect('wp_testing_model_formulas::formula_source[0]').to.have.fieldValue('scale-lie > 10')
        })
    })

    it('should error when formulas is bad', function() {
        casper.then(function() {
            this.click('.wpt_formulas_helper input[data-source="scale-lie"]')
            this.click('.wpt_formulas_helper input[data-source="<="]')
            this.sendKeys('input[name="wp_testing_model_formulas::formula_source[1]"]', 'nothing');
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
