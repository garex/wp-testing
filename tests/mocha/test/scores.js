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
            this.fillSelectors('form#post', {
                '#title': 'Simple Test With Scores',
                '#wpt_question_title_0': 'Does tomato red?',
                '#wpt_question_title_1': 'Do you like tomatos?',
                '#wpt_question_title_2': 'Are you Pinokkio?'
            })
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '#wpt_edit_formulas input[value="Lie, ∑ "]'.should.be.inDOM
            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '-1',
                '#wpt_score_value_1_0': '5',
                '#wpt_score_value_2_0': '0'
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
            this.fillSelectors('form#post', {
                '#wpt_score_value_2_0': 'bad value'
            }, true)
        })

        casper.waitForUrl(/post/, function() {
            'Fatal'.should.not.be.textInDOM
            'Test data not saved'.should.be.textInDOM
            'Score Value: Please enter a whole number'.should.be.textInDOM
            this.clickLabel('« Back')
        })

        casper.waitForUrl(/edit/, function() {
            this.fillSelectors('form#post', {
                '#wpt_score_value_2_0': '0'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should be empties in case of zeros', function() {
        casper.then(function() {
            'wpt_score_value_2_0.value'.should.evaluate.to.be.equal('')
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
