describe('Multiple answers test', function() {

    var isOpened = null

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

    afterEach(function() {
        if (false === isOpened) {
            throw new Error('Page not opened so other checks is not actual now')
        }
    })

    it('should be created', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.fillSelectors('form#post', {
                '#title': 'Multiple Answers',
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.click('.misc-pub-wpt-test-page-reset-answers-on-back input[type=checkbox]')
            this.click('.misc-pub-wpt-test-page-multiple-answers input[type=checkbox]')
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' No',  'label')
            this.clickLabel(' Lie', 'label')
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
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

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '∑ 10'.should.be.textInDom
        })
    })

    it('should be opened', function() {
        isOpened = false
        casper.open('http://wpti.dev/?wpt_test=multiple-answers').waitForUrl(/multiple-answers/, function() {
            'Multiple Answers'.should.be.textInDOM
        }).then(function() {
            isOpened = true
        })
    })

    it('should have title without percents', function() {
        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            this.getTitle().should.match(/^Multi/)
        })
    })

    it('should have button disabled', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
        })
    })

    it('should not have percentage in title initially', function() {
        casper.then(function() {
            this.getTitle().should.not.match(/^\d+% ans/)
        })
    })

    it('should have percentage in title after 1st question click', function() {
        casper.then(function() {
            this.clickLabel('No')
            this.getTitle().should.match(/^50% ans/)
        })
    })

    it('should have same percentage after 1st question click on 2nd answer', function() {
        casper.then(function() {
            this.clickLabel('Yes')
            this.getTitle().should.match(/^50% ans/)
        })
    })

    it('should have zero percentage after 1st question answers unclicks', function() {
        casper.then(function() {
            this.clickLabel('Yes')
            this.clickLabel('No')
            this.getTitle().should.match(/^0% ans/)
        })
    })

    function clickAllAnswers() {
        casper.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
        casper.clickLabel('No',  '*[@id="wpt-test-form"]/*[1]/*//label')
        casper.clickLabel('Yes', '*[@id="wpt-test-form"]/*[2]/*//label')
        casper.clickLabel('No',  '*[@id="wpt-test-form"]/*[2]/*//label')
    }
    it('should have all percentage after all answers clicks', function() {
        casper.then(function() {
            clickAllAnswers()
            this.getTitle().should.match(/^100% ans/)
        })
    })

    it('should have button enabled after all answers clicks', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
        })
    })

    it('should have zero percentage after all answers unclicks', function() {
        casper.then(function() {
            clickAllAnswers()
            this.getTitle().should.match(/^0% ans/)
        })
    })

    it('should have button disabled after all answers clicks', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
        })
    })

    it('should open result page', function() {
        isOpened = false
        casper.then(function() {
            clickAllAnswers()
            this.fill('form#wpt-test-form', {}, true)
        }).waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            isOpened = true
        })
    })

    it('should have scale with all answers sum', function() {
        casper.then(function() {
            '10 out of 10'.should.be.textInDOM
        })
    })

    it('should reset answers on back', function() {
        casper.back().then(function() {
            'Results'.should.not.be.textInDOM
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
            this.getTitle().should.match(/^Multi/)
        })
    })
})
