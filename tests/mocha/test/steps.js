describe('Steps', function() {

    var isOpened = null,
        site     = 'http://wpti.dev',
        wp       = ''

    before(function () {
        this.timeout(3600000)
        casper.start(site + wp + '/wp-admin/').thenOpen(site + wp + '/wp-login.php', {
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

            this.evaluate(function() {
                jQuery('#edButtonHTML,#content-html').addClass('__text_tab_here')
            })
            this.click('.__text_tab_here')

            this.fillSelectors('form#post', {
                '#title': 'Three Steps',
                '#content': 'The step is composed of the tread and riser.',
                '#wpt_question_title_0': 'How many steps a day should a person take?',
                '#wpt_question_title_2': 'How many steps in a kilometer?',
                '#wpt_question_title_3': 'How many steps are there in accident prevention?'
            })
            this.click('.misc-pub-wpt-test-page-one-question-per-step input[type=checkbox]')
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
                '#wpt_score_value_0_1': '1',
                '#wpt_score_value_1_0': '1',
                '#wpt_score_value_1_1': '1',
                '#wpt_score_value_2_0': '1',
                '#wpt_score_value_2_1': '1'
            })

            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            '∑ 6'.should.be.textInDom
            this.evaluate(function() {
                document.location = jQuery('#view-post-btn a').attr('href')
            })
        })
    })

    it('should be opened', function() {
        isOpened = false
        casper.open(site + '/?wpt_test=three-steps').waitForUrl(/three-steps/, function() {
            'Three Steps'.should.be.textInDOM
        }).then(function() {
            isOpened = true
        })
    })

    it('should has description on 1st step', function() {
        casper.then(function() {
            'The step is composed of the tread and riser.'.should.be.textInDOM
        })
    })

    it('should not have percentage on 1st step', function() {
        casper.then(function() {
            this.getTitle().should.not.match(/^\d+% ans/)
        })
    })

    it('should have one question per step and no second', function() {
        casper.then(function() {
            'How many steps a day should a person take?'.should.be.textInDOM
            'How many steps in a kilometer?'.should.not.be.textInDOM
        })
    })

    it('should show step counter on 1st step', function() {
        casper.then(function() {
            '1 out of 3'.should.be.textInDOM
        })
    })

    it('should have Next button and not Get Results', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit][value="Next"]'.should.be.inDOM
            '#wpt-test-form input[type=submit][value="Get Test Results"]'.should.not.be.inDOM
        })
    })

    it('should have 33% answered and next non-disabled after answer', function() {
        casper.then(function() {
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('No',  '*[@id="wpt-test-form"]/*[1]/*//label')
            this.getTitle().should.match(/^33% ans/)
            '#wpt-test-form input[type=submit]'.should.not.have.attr('disabled')
        })
    })

    it('should open 2nd step', function() {
        isOpened = false
        casper.then(function() {
            this.fill('form#wpt-test-form', {}, true)
        }).waitForUrl(/three-steps/, function() {
            'Fatal'.should.not.be.textInDOM
            '2 out of 3'.should.be.textInDOM
            isOpened = true
        })
    })

    it('should not has description on 2nd step', function() {
        casper.then(function() {
            'The step is composed of the tread and riser.'.should.not.be.textInDOM
        })
    })

    it('should have 33% answered on 2nd step initially', function() {
        casper.then(function() {
            this.getTitle().should.match(/^33% ans/)
            '#wpt-test-form input[type=submit]'.should.have.attr('disabled')
        })
    })

    it('should has question number "2." and not "1."', function() {
        casper.then(function() {
            '2.How many steps in a kilometer?'.should.be.textInDOM
        })
    })

    it('should open last step', function() {
        isOpened = false
        casper.then(function() {
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('No',  '*[@id="wpt-test-form"]/*[1]/*//label')
            this.fill('form#wpt-test-form', {}, true)
        }).waitForUrl(/three-steps/, function() {
            'Fatal'.should.not.be.textInDOM
            '3.'.should.be.textInDOM
            isOpened = true
        })
    })

    it('should have Get Results button and not Next', function() {
        casper.then(function() {
            '#wpt-test-form input[type=submit][value="Get Test Results"]'.should.be.inDOM
            '#wpt-test-form input[type=submit][value="Next"]'.should.not.be.inDOM
        })
    })

    it('should not show step counter on last step', function() {
        casper.then(function() {
            'out of 3'.should.not.be.textInDOM
        })
    })

    it('should show results page after click', function() {
        isOpened = false
        casper.then(function() {
            this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
            this.clickLabel('No',  '*[@id="wpt-test-form"]/*[1]/*//label')
            this.wait(300).fill('form#wpt-test-form', {}, true)
        }).waitForUrl(/three-steps/, function() {
            'Fatal'.should.not.be.textInDOM
            'Results'.should.be.textInDOM
            'Lie'.should.be.textInDOM
            '6 out of 6'.should.be.textInDOM
            isOpened = true
        })
    })

})
