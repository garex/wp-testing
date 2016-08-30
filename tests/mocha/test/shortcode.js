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
        }, null, 10000)

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

    describe('wpt_test_first_page non-public and not existent', function() {
        var testEditUrl = ''

        it('should unpublish test', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                this.clickLabel('Test Containing Results')
            })

            casper.waitForUrl(/edit/, function() {
                testEditUrl = this.getCurrentUrl()
                this.fillSelectors('form#post', {
                    '#post_status': 'pending'
                })
                this.click('#publish')
            })

            casper.waitForUrl(/message/, function() {
                '#message'.should.be.inDOM
            })
        })

        it('should be added',
            addShortcodeAndCheckResult('[wpt_test_first_page name=test-containing-results]\n[wpt_test_first_page name=unexistent]', function() {
            'wpt_test_first_page'.should.be.textInDOM
        }))

        it('should error on non-published test', function() {
            casper.then(function() {
                'wpt_test_first_page: Test "Test Containing Results" is not published. You can not include it anywhere.'.should.be.textInDOM
            })
        })

        it('should error of not found test', function() {
            casper.then(function() {
                'wpt_test_first_page: Can not find test by id or name'.should.be.textInDOM
            })
        })

        it('should publish test back', function() {
            casper.thenOpen(testEditUrl, function() {
                this.click('#publish')
            })

            casper.waitForUrl(/message/, function() {
                '#message'.should.be.inDOM
            })
        })
    })

    describe('wpt_test_first_page: two tests updates each own`s percentage in title', function() {
        it('should be added',
            addShortcodeAndCheckResult('[wpt_test_first_page name=eysencks-personality-inventory-epi-extroversionintroversion]\n[wpt_test_first_page name=sorted-answers]', function() {
            'The Eysenck Personality Inventory (EPI) measures two pervasive'.should.be.textInDOM
            'Sorted Answers'.should.be.textInDOM
        }))

        it('should not have percentage initially', function() {
            casper.then(function() {
                this.getTitle().should.not.match(/^\d+% ans/)
            })
        })

        it('should change percentage from 1st test', function() {
            casper.then(function() {
                this.clickLabel('Yes', '*[contains(@action, "personality")]/*[1]/*//label')
                this.getTitle().should.match(/^2% ans/)
            })
        })

        it('should change percentage to 100% in 2nd test', function() {
            casper.then(function() {
                this.clickLabel('Yes', '*[contains(@action, "sorted")]/*[1]/*//label')
                this.getTitle().should.match(/^100% ans/)
            })
        })

        it('should change percentage back to 1st test', function() {
            casper.then(function() {
                this.clickLabel('Yes', '*[contains(@action, "personality")]/*[2]/*//label')
                this.getTitle().should.match(/^4% ans/)
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

    describe('wpt_test_* itself include', function() {
        it('should add shortcode to test', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                this.clickLabel('Scale Aggregates')
            })

            casper.waitForUrl(/edit/, function() {
                this.fillSelectors('form#post', {
                    '#content': 'Before shortcode\n\n[wpt_test_read_more name=scale-aggregates]\n\nAfter shortcode',
                })
                this.click('#publish')
            })
        })

        it('should open test for preview', function() {
            casper.waitForUrl(/message/, function() {
                '#message'.should.be.inDOM
                this.evaluate(function() {
                    document.location = jQuery('#post-preview').attr('href')
                })
            })
        })

        it('should error about itself include', function() {
            casper.then(function() {
                'Scale Aggregates'.should.be.textInDOM
                'wpt_test_read_more: Shortcode "wpt_test_read_more#scale-aggregates" includes itself'.should.be.textInDOM
            })
        })
    })

    describe('not fails other rendering', function() {
        var content = '[caption align="aligncenter" width="300" caption="Caption1"]'

        it('should create new scale with caption shortcode inside', function() {
            casper.thenOpen(server + '/wp-admin/', function() {
                this.clickLabel('Scales', '*[@id="menu-posts-wpt_test"]/*//a')
            })

            casper.then(function() {
                'Scales'.should.be.inTitle

                this.fill('form#addtag', {
                    'tag-name'    : 'Scale Caption',
                    'description' : content
                }, true)

                this.waitForText('Scale Caption')
            })
        })

        it('should create new test with one question, answer, created scale and caption shortcode in description', function() {
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

                this.clickLabel(' Scale Caption', 'label')
                this.clickLabel(' Yes', 'label')
                this.click('#wpt_question_add');
                this.fillSelectors('form#post', {
                    '#title': 'Test With Caption Shortcodes',
                    '#content': content,
                    '#wpt_question_title_0': 'First or last?'
                }, true)
            })

            casper.waitForUrl(/message/, function() {
                'Fatal'.should.not.be.textInDOM
                '#message'.should.be.inDOM
                this.fillSelectors('form#post', {
                    '#wpt_score_value_0_0': '1',
                }, true)
            })
        })

        it('should open created test', function() {
            casper.evaluate(function() {
                document.location = jQuery('#post-preview').attr('href')
            })
            casper.waitForUrl(/wpt_test/)
        })

        it('should show that question and answer are rendered', function() {
            casper.then(function() {
                'First or last?'.should.be.textInDOM
                'Yes'.should.be.textInDOM
            })
        })

        it('should show that shortcode also rendered', function() {
            casper.then(function() {
                '[caption'.should.not.be.textInDOM
                'Caption1'.should.be.textInDOM
            })
        })

        it('should open result page', function() {
            casper.then(function() {
                this.clickLabel('Yes', '*[starts-with(@id, "wpt-test-form")]/*[1]/*//label')
                this.fill('form.wpt_test_form', {}, true)
            }).waitForUrl(/wpt_passing_slug.+[a-z0-9]+[a-f0-9]{32}/, function() {
                'Fatal'.should.not.be.textInDOM
                'Results'.should.be.textInDOM
            })
        })

        it('should show that shortcode in scale rendered', function() {
            casper.then(function() {
                'Scale Caption'.should.be.textInDOM
                '[caption'.should.not.be.textInDOM
                'Caption1'.should.be.textInDOM
            })
        })
    })
})
