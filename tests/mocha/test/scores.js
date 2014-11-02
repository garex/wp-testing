describe('Scores', function() {

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

    it('should be added in simple test', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Lie', 'label')
            this.fill('form#post', {
                'post_title': 'Simple Test With Scores',
                'wp_testing_model_questions::question_title[0]': 'Does tomato red?',
                'wp_testing_model_questions::question_title[1]': 'Do you like tomatos?',
                'wp_testing_model_questions::question_title[2]': 'Are you Pinokkio?'
            })
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ "]'.should.be.inDOM
            this.fill('form#post', {
                'wp_testing_model_questions::wp_testing_model_score::score_value[0][0]': '-1',
                'wp_testing_model_questions::wp_testing_model_score::score_value[1][0]': '5',
                'wp_testing_model_questions::wp_testing_model_score::score_value[2][0]': '0'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ 4"]'.should.be.inDOM
        })
    })

    it('should allow to save only numbers', function() {
        casper.then(function() {
            this.fill('form#post', {
                'wp_testing_model_questions::wp_testing_model_score::score_value[2][0]': 'bad value'
            }, true)
        })

        casper.waitForUrl(/post/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test data not saved'.should.be.textInDOM
            'Score Value: Please enter a whole number'.should.be.textInDOM
            this.clickLabel('« Back')
        })

        casper.waitForUrl(/edit/, function() {
            this.fill('form#post', {
                'wp_testing_model_questions::wp_testing_model_score::score_value[2][0]': '0'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should be empties in case of zeros', function() {
        casper.then(function() {
            expect('input[name="wp_testing_model_questions::wp_testing_model_score::score_value[2][0]"]').to.have.fieldValue(null)
        })
    })

    it('should have total sum by each scale', function() {
        casper.then(function() {
            '#wpt_edit_formulas input[value="Lie, ∑ 4"]'.should.be.inDOM
        })
    })

    it('should be cleared in test in case of scale toggle', function() {
        casper.then(function() {
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ 4"]'.should.not.be.inDOM
        })

        casper.then(function() {
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ "]'.should.be.inDOM
        })
    })
})
