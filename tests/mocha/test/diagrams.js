describe('Diagrams', function() {

    var testEditUrl =
        testViewUrl =
        resultUrl   = null

    before(function () {
        this.timeout(3600000)
        casper.start('http://wpti.dev/wp-admin/').viewport(400, 300).thenOpen('http://wpti.dev/wp-login.php', {
            method: 'post',
            data  : {
                log: 'wpti',
                pwd: 'wpti'
            }
        })
    })

    afterEach(function() {
        if ('' === testEditUrl) {
            throw new Error('Test not created so other checks is not actual now')
        }

        if ('' === testViewUrl) {
            throw new Error('Test view page is not accessible so other checks is not actual now')
        }

        if ('' === resultUrl) {
            throw new Error('Result not opened so other checks is not actual now')
        }
    })

    function createTest(options) {return function() {
        testEditUrl =
        testViewUrl = ''

        casper.thenOpen('http://wpti.dev/wp-admin/').waitForUrl(/admin/, function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.fillSelectors('form#post', {
                '#title': options.title,
                '#wpt_question_title_0': 'Question 1?',
                '#wpt_question_title_1': 'Question 2?'
            })
            this.clickLabel(' Yes', 'label')
            this.clickLabel(' Extraversion/Introversion', 'label')
            this.clickLabel(' Lie', 'label')
            this.clickLabel(' Neuroticism/Stability', 'label')

            if (options.isEnableDiagram) {
                this.click('.misc-pub-wpt-result-page-show-scales-diagram input[type=checkbox]')
            }
        })

        if (options.isAddScales) {
            casper.then(function () {
                this.click('#wpt_scale-add-toggle')
                this.sendKeys('#newwpt_scale', 'Scale 4')
                this.click('#wpt_scale-add-submit')
            }).wait(1000, function() {
                this.sendKeys('#newwpt_scale', 'Scale 5')
                this.click('#wpt_scale-add-submit')
            }).wait(1000, function() {
                this.sendKeys('#newwpt_scale', 'Scale 6')
                this.click('#wpt_scale-add-submit')
            }).wait(1000)
        }

        casper.then(function() {
            this.fill('form#post', {}, true)
        }).waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '5',
                '#wpt_score_value_0_1': '2',
                '#wpt_score_value_0_2': '4',
                '#wpt_score_value_1_0': '0',
                '#wpt_score_value_1_1': '3',
                '#wpt_score_value_1_2': '1'
            })

            this.click('#publish')
        }, null, 10000)

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM

            testEditUrl = this.getCurrentUrl()
            testViewUrl = this.getElementAttribute('#view-post-btn a', 'href')
        })

    }}

    it('should create standard test without diagram and same length scales', createTest({
        title           : 'Diagram with few scales of same length',
        isEnableDiagram : false,
        isAddScales     : false
    }))

    function openTestResult() {return function() {
        resultUrl = ''

        casper.thenOpen(testViewUrl).waitForUrl(/test/, function() {
            this.clickLabel('Yes')
            this.fill('form#wpt-test-form', {}, true)
        })

        casper.waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function() {
            'Results'.should.be.textInDOM
            resultUrl = this.getCurrentUrl()
        })
    }}

    it('should open result for test with few scales', openTestResult())

    it('should not have diagram initially', function() {
        casper.then(function() {
            '.scales.diagram'.should.not.be.inDOM
        })
    })

    it('should enable diagram option', function() {
        casper.thenOpen(testEditUrl, function() {
            this.click('.misc-pub-wpt-result-page-show-scales-diagram input[type=checkbox]')
            this.click('#publish')
        }).waitForUrl(/message/)
    })

    it('should have diagram after enable', function() {
        casper.thenOpen(resultUrl, function() {
            '.scales.diagram'.should.be.inDOM
        })
    })

    it('should have nice diagram`s height', function() {
        casper.then(function() {
            var bounds = this.getElementBounds('.scales.diagram')
            bounds.height.should.be.above(10)
            bounds.height.should.be.within(bounds.width/1.7, bounds.width/1.6)
        })
    })

    it('should have circles as scales values', function() {
        casper.then(function() {
            'document.querySelectorAll("circle").length'.should.evaluate.to.be.equal(3)
        })
    })

    it('should have text labels that fit to width', function() {
        casper.then(function() {
            'document.querySelector("tspan").textContent'.should.evaluate.match(/^E.+\.\.\.$/)
        })
    })

    it('should not have percentages with same scales lengths', function() {
        casper.then(function() {
            '100%'.should.not.be.textInDOM
        })
    })

    it('should create test with different scales lengths and many scales', createTest({
        title           : 'Diagram with many scales  of different length',
        isEnableDiagram : true,
        isAddScales     : true
    }))

    it('should open result for test with many scales', openTestResult())

    it('should have percentages with different scales lengths', function() {
        casper.thenOpen(resultUrl, function() {
            'Scale 4'.should.be.textInDOM
            'Scale 5'.should.be.textInDOM
            'Scale 6'.should.be.textInDOM
            '100%'.should.be.textInDOM
        })
    })

    it('should not have text labels fit to width as they are rotated', function() {
        casper.thenOpen(resultUrl, function() {
            'Neuroticism/Stability'.should.be.textInDOM
            this.getElementAttribute('text', 'transform').should.not.be.empty
        })
    })

})
