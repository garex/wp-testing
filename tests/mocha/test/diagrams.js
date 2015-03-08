describe('Diagrams', function() {

    var testEditUrl =
        testViewUrl =
        resultUrl   = null

    before(function () {
        this.timeout(3600000)
        casper.start('http://wpti.dev/wp-admin/').viewport(400, 1000).thenOpen('http://wpti.dev/wp-login.php', {
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

            if (!options.isShowScales) {
                this.click('.misc-pub-wpt-result-page-show-scales input[type=checkbox]')
            }
        })

        casper.then(function() {
            this.fill('form#post', {}, true)
        }).waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM

            this.fillSelectors('form#post', {
                '#wpt_score_value_0_0': '5',
                '#wpt_score_value_0_1': '2',
                '#wpt_score_value_0_2': '4',
                '#wpt_score_value_1_0': options.isSameLength ? '0' : '5',
                '#wpt_score_value_1_1': options.isSameLength ? '3' : '0',
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
        title           : 'Diagram with same length scales',
        isEnableDiagram : false,
        isShowScales    : true,
        isSameLength    : true
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

    it('should create test with different scales lengths', createTest({
        title           : 'Diagram with different length scales uses percents',
        isEnableDiagram : true,
        isShowScales    : false,
        isSameLength    : false
    }))

    it('should open result for test with different scales lengths', openTestResult())

    it('should have percentages when different scales lengths', function() {
        casper.then(function() {
            '80%'.should.be.textInDOM
        })
    })

    it('should show annotations on mouse hover', function() {
        casper.then(function() {
            'Neuroticism or emotionality is characterized by high levels of negative affect'.should.not.be.textInDOM
            this.mouse.move('.scales.diagram')
            '2 out of 2'.should.be.textInDOM
        })
    })
})
