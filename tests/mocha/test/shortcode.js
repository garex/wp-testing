describe('Shortcode', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    function addShortcodeAndCheckResult(shortcode, thenOnPage1) {return function() {
        casper.thenOpen(server + '/wp-admin/post.php?post=1&action=edit', function() {
            this.evaluateOrDie(function() {
                return /Edit Post/.test(document.body.innerText)
            })

            this.evaluate(function() {
                jQuery('#edButtonHTML,#content-html').addClass('__text_tab_here')
            })

            this.click('.__text_tab_here')

            this.fillSelectors('form#post', {
                '#title'   : 'Hi World!',
                '#content' : 'Hello World!\n' + shortcode
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            '#message'.should.be.inDOM
        })

        casper.thenOpen(server + '/?p=1', thenOnPage1)
    }}

    describe('[wpt_tests reverse=id list=square]', function() {
        it('should be added and contain first test',
            addShortcodeAndCheckResult('[wpt_tests reverse=id list=square]', function() {
            '.wp-testing.shortcode.tests'.should.be.inDOM
            '.wp-testing.shortcode.tests li:last-child'.should.contain.text('EPI')
        }))
    })

    describe('[wpt_tests list=unknown]', function() {
        it('should be added and contain error with guide',
            addShortcodeAndCheckResult('[wpt_tests list=unknown]', function() {
            '.wp-testing.shortcode.tests'.should.not.be.inDOM
            'body'.should.not.contain.text('EPI')
            'pre.error-message'.should.be.inDOM
            'pre.error-message'.should.contain.text('wpt_tests: Value "unknown" for attribute "list" is not in allowed list')
            'pre.error-message'.should.contain.text('w3.org')
        }))
    })

    describe('[wpt_tests reverse=id max=3 list=square class=my-list]', function() {
        it('should be added', addShortcodeAndCheckResult('[wpt_tests reverse=id max=3 list=square class=my-list]', function() {
            '.wp-testing.shortcode.my-list'.should.be.inDOM
        }))

        it('should be ordered in reverse order', function() {
            casper.then(function() {
                '.wp-testing.shortcode.tests li:last-child'.should.not.contain.text('EPI')
            })
        })

        it('should respect max', function() {
            casper.then(function() {
                'document.querySelectorAll(".wp-testing.shortcode.tests li").length'.should.evaluate.to.be.equal(3)
            })
        })

        it('should set list style type', function() {
            casper.then(function() {
                'document.querySelector(".wp-testing.shortcode.tests").style.listStyleType'.should.evaluate.to.be.equal('square')
            })
        })
    })

    describe('[wpt_test_read_more start_title=Cool class=wow name=eysencks-personality-inventory-epi-extroversionintroversion]', function() {
        it('should be added',
            addShortcodeAndCheckResult('[wpt_test_read_more start_title=Cool class=wow name=eysencks-personality-inventory-epi-extroversionintroversion]', function() {
            '.wp-testing.shortcode.test-read-more'.should.be.inDOM
        }))

        it('should contain only title and text before read more', function() {
            casper.then(function() {
                '.wp-testing.shortcode.test-read-more'.should.contain.text('EPI')
                '.wp-testing.shortcode.test-read-more'.should.contain.text('Each form contains 57')
                '.wp-testing.shortcode.test-read-more'.should.not.contain.text('To interpret the scores')
            })
        })

        it('should have added CSS class', function() {
            casper.then(function() {
                '.wp-testing.shortcode.test-read-more.wow'.should.be.inDOM
            })
        })

        it('should have button with "Cool" title', function() {
            casper.then(function() {
                '.wp-testing.shortcode.test-read-more form input[value="Cool"]'.should.be.inDOM
            })
        })
    })

    describe('[wptlist]', function() {
        it('should be added and contain first test',
            addShortcodeAndCheckResult('[wptlist]', function() {
            '.wp-testing.shortcode.tests'.should.be.inDOM
            '.wp-testing.shortcode.tests li:first-child'.should.contain.text('EPI')
        }))
    })
})
