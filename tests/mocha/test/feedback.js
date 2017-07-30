describe('Feedback', function() {

    var server = require('../env').server()
    before(function () {
        require('../login-as').admin(this)
    })

    describe('Add review', function() {
        it('should have add review link in tests page', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                'Add review'.should.be.textInDOM
            })
        })

        it('should not have "thank you" text', function() {
            casper.then(function() {
                'Thank you'.should.not.be.textInDOM
            })
        })

        it('should open review page on rate us link', function() {
            casper.then(function() {
                this.clickLabel('Add review')
            }).wait(500)
        })

        it('should show "thank you" text after reload', function() {
            casper.thenOpen(server + '/wp-admin/edit.php?post_type=wpt_test', function() {
                'Add review'.should.not.be.textInDOM
                'Thank you'.should.be.textInDOM
            })
        })
    })

    describe('Editor metabox', function() {
        it('should exists at test edit page', function() {
            casper.thenOpen(server + '/wp-admin/post-new.php?post_type=wpt_test', function() {
                '#wpt_feedback'.should.be.inDOM
            })
        })

        it('should have "thank you" text as before we clicked it', function() {
            casper.then(function() {
                expect('#wpt_feedback').to.contain.text('Thank you')
            })
        })
    })

    describe('Report the problem', function() {
        it('should open in new window', function() {
            casper.then(function() {
                this.clickLabel('Report the problem', '*[@id="wpt_feedback"]//a')
            })

            casper.waitForPopup(/wpt_feedback_report_issue/)
        })

        it('should fill Report the problem form with tech details', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    this.fill('form#report-issue', {
                        'issue_repeats[other_hosting]':    true,
                        'issue_repeats[other_plugins_disabled]': true,
                        'expected': 'Expected text.',
                        'actual': 'Actual text.',
                        'screenshot': 'http://site.com/wtf.jpg',
                        'environment': true
                    }, true)
                })
            })
        })

        it('should go to environment step', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.waitWhileSelector('form#report-issue')
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    'System information'.should.be.textInIDOM
                    'PHP version'.should.be.textInIDOM
                    'Active theme stylesheet'.should.be.textInIDOM
                })
            })
        })

        it('should select all parameters and go next', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.then(function() {
                    this.clickLabel('Select All')
                    this.click('#submit')
                })
            })
        })

        it('should open final step with textarea', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.waitForText('Copy this text to create new topic')
            })
        })

        it('should have issue', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    '<strong>Issue repeats</strong>'.should.be.textInDOM
                    '<li>another hosting</li>'.should.be.textInDOM
                    '<li>other plugins disabled</li>'.should.be.textInDOM
                    'on the local computer'.should.not.be.textInDOM

                    'Expected text.'.should.be.textInDOM
                    'Actual text.'.should.be.textInDOM

                    '<img src="http://site.com/wtf.jpg" alt="Screenshot" />'.should.be.textInDOM
                })
            })
        })

        it('should have tech details', function() {
            casper.withPopup(/wpt_feedback_report_issue/, function() {
                casper.then(function() {
                    '<strong>Technical details</strong>'.should.be.textInDOM
                    '<code>PHP extensions</code>'.should.be.textInDOM
                    'SPL'.should.be.textInDOM
                })
            })
        })
    })

    describe('Get support', function() {
        it('should open in new window', function() {
            casper.thenOpen(server + '/wp-admin/post-new.php?post_type=wpt_test', function() {
                '#wpt_feedback'.should.be.inDOM
            })

            casper.then(function() {
                this.clickLabel('Get the support', '*[@id="wpt_feedback"]//a')
            })

            casper.waitForPopup(/wpt_feedback_get_support/)
        })

        it('should fill get support form with asap checkbox', function() {
            casper.withPopup(/wpt_feedback_get_support/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    this.fill('form#get-support', {
                        'title':   'In short',
                        'details': 'Details here.',
                        'asap':    true
                    }, true)
                }).waitWhileSelector('form#get-support')
            })
        })

        it('should have paid support texts visible', function() {
            casper.withPopup(/wpt_feedback_get_support/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM
                }).waitWhileVisible('h1.asap-0').waitUntilVisible('h1.asap-1', function() {
                    expect('h1.asap-1').to.contain.text('as soon as possible')
                }).waitWhileVisible('p.asap-0').waitUntilVisible('.text-to-html.asap-1', function() {
                    expect('.text-to-html.asap-1').to.contain.text('In short')
                    expect('.text-to-html.asap-1').to.contain.text('Details here.')
                })
            })
        })

        it('should allow to toggle paid support', function() {
            casper.withPopup(/wpt_feedback_get_support/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    this.clickLabel('Paid support', 'label')
                })
            })
        })


        it('should show then default support texts', function() {
            casper.withPopup(/wpt_feedback_get_support/, function() {
                casper.then(function() {
                    'Fatal'.should.not.be.textInDOM

                    casper.waitWhileVisible('h1.asap-1').waitUntilVisible('h1.asap-0', function() {
                        expect('h1.asap-0').to.not.contain.text('as soon as possible')
                    }).waitWhileVisible('p.asap-1').waitUntilVisible('.asap-0 textarea', function() {
                        expect('.asap-0 textarea').to.contain.text('In short')
                        expect('.asap-0 textarea').to.contain.text('Details here.')
                    })
                })
            })
        })

    })
})
