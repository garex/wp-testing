describe('Scale aggregates', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    it('should create test with single answer', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.click('#wpt_question_add');
            this.click('#wpt_question_add');
            this.fillSelectors('form#post', {
                '#title': 'Scale Aggregates',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' No',  'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '1',
                '#wpt_score_value_0_1': '2',
                '#wpt_score_value_1_0': '3',
                '#wpt_score_value_1_1': '4'
            })

            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should have different sum and max in single answer mode', function() {
        casper.then(function() {
            '∑ 10'.should.be.textInDOM
            'max 6'.should.be.textInDOM
        })
    })

    it('should switch test into multi answer mode', function() {
        casper.then(function() {
            this.click('.misc-pub-wpt-test-page-multiple-answers input[type=checkbox]')
            this.click('#publish')
        })

        casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })
    })

    it('should have only sum in multiple answer mode', function() {
        casper.then(function() {
            '∑ 10, max 6'.should.not.be.textInDOM
            '∑ 10'.should.be.textInDOM
        })
    })
})
