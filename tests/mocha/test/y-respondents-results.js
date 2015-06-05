describe('Respondents results', function() {

    before(function () {
        require('../login-as').admin(this)
    })

describe('Basic', function() {
    it('should be in admin area', function() {
        casper.then(function() {
            this.clickLabel('Respondents’ results', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.waitForUrl(/respondents/, function() {
            'Respondents'.should.be.inTitle
            'Fatal'.should.not.be.textInDOM
        })
    })

    it('should have items', function() {
        casper.then(function() {
            '0 items'.should.not.be.textInDom
        })
    })

    it('should have user agent column with values', function() {
        casper.then(function() {
            'Browser'.should.be.textInDom
            'PhantomJS'.should.be.textInDom
        })
    })

    it('should have tests titles', function() {
        casper.then(function() {
            'Test Containing Results'.should.be.textInDom
            'Are You Hot or Not?!'.should.be.textInDom
            'Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)'.should.be.textInDom
            'Simple Test With Scores'.should.not.be.textInDom
        })
    })

    it('should have usernames attached to results', function() {
        casper.then(function() {
            "document.querySelectorAll('.column-user strong > a').length".should.evaluate.to.be.at.least(2)
        })
    })

})

describe('Filtering', function() {

    it('should filter by test', function() {
        casper.then(function() {
            this.evaluate(function() {
                jQuery('select[name="filter_condition[test_id]"]')
                    .find('option:contains("Are You Hot or Not?")')
                        .attr('selected', 'selected')
            })
            this.fill('form#passings-filter', {}, true)
        })

        casper.waitForUrl(/filter_condition[^=]+test_id[^=]+=\d+/, function() {
            '2 items'.should.be.textInDOM
            expect('#the-list').to.contain.text('Are You Hot or Not?!')
            expect('#the-list').to.not.contain.text('Test Containing Results')
        })
    })

    it('should filter by user also', function() {
        casper.then(function() {
            this.fill('form#passings-filter', {
                'filter_condition[user]': '-'
            }, true)
        })

        casper.waitForUrl(/filter_condition[^=]+user[^=]+=[^&]/, function() {
            '1 item'.should.be.textInDOM
            expect('#the-list').to.not.contain.text('user')
        })
    })

    it('should filter just by user', function() {
        casper.then(function() {
            this.fill('form#passings-filter', {
                'filter_condition[test_id]' : '',
                'filter_condition[user]'    : 'user'
            }, true)
        })

        casper.waitForUrl(/filter_condition[^=]+user[^=]+=[^&]/, function() {
            '4 items'.should.be.textInDOM
            expect('#the-list').to.contain.text('user')
        })
    })
})

describe('View results', function() {
    var firstResult = null

    it('should allow to view test results', function() {
        casper.then(function() {
            this.clickLabel('View', '*/strong/a[@class="row-title"]')
        })

        casper.waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Results'.should.be.textInDom
            ' out of '.should.be.textInDom
            firstResult = this.evaluate(function() {
                return document.location.href
            })

            this.back()
        })
    })

    it('should hide result to trash', function() {
        casper.then(function() {
            this.clickLabel('Trash', '*/a[@class="submitdelete"]')
        })

        casper.waitForSelector('.subsubsub .trash', function() {
            'Trash (1)'.should.be.textInDOM
        })
    })

    it('should not allow to view trashed test result', function() {
        casper.thenOpen(firstResult, function() {
            'Results'.should.not.be.textInDom
            ' out of '.should.not.be.textInDom
            'Test result not found'.should.be.textInDom
        })

        casper.back()
    })

    it('should restore passing from trash', function() {
        casper.then(function() {
            this.click('li.trash a')
        })

        casper.waitForUrl(/passing_status=trash/, function() {
            this.clickLabel('Untrash', '*/strong/a[@class="row-title"]')
        })

        casper.waitForSelector('.subsubsub .all .current', function() {
            'Trash (1)'.should.not.be.textInDOM
        })
    })
})

describe('Sorting', function() {

    it('should sort results by date', function() {
        casper.then(function() {
            this.evaluate(function() {
                return jQuery('#the-list td:first').text()
            }).should.not.be.equal('1')

            this.click('#passing_created a')
        })

        casper.waitForUrl(/order=asc/, function() {
            this.evaluate(function() {
                return jQuery('#the-list td:first').text()
            }).should.be.equal('1')
        })
    })
})

describe('Respondent', function() {

    it('should login under user', function() {
        require('../login-as').adminLogout()
        require('../login-as').user()
    })

    it('should have tests in menu', function() {
        casper.then(function() {
            this.clickLabel('Tests', '*')
        })
    })

    it('should view own results in admin area', function() {
        casper.waitForUrl(/wpt_test_user_results/, function () {
            'Test Containing Results'.should.be.textInDOM
            'Are You Hot or Not?!'.should.be.textInDOM
        })
    })

    it('should have user-specific page title', function() {
        casper.then(function () {
            'Results'.should.be.textInDOM
            'Respondents’ test results'.should.not.be.textInDOM
        })
    })

    it('should have same results count as under admin with filter by user', function() {
        casper.then(function () {
            '4 items'.should.be.textInDOM
        })
    })
})

})
