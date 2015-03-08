var casper = require('casper').create()
casper.options.viewportSize = {width: 1060, height: 1016}

casper.start('http://wpti.dev/').thenOpen('http://wpti.dev/wp-login.php', {
    method: 'post',
    data  : {
        log: 'wpti',
        pwd: 'wpti'
    }
})

var screenshots = [
   {
       title   : 'Test editing section with menu in admin',
       actions : function () {
           casper.thenOpen('http://wpti.dev/wp-admin/profile.php', function() {
               this.clickLabel('Midnight', 'label')
               this.fill('form#your-profile', {
                   nickname     : 'Tests Author',
                   display_name : 'Tests Author',
                   email        : 'ustimenko.alexander@gmail.com'
               }, true)
           }).waitForUrl(/updated/)

           casper.thenOpen('http://wpti.dev/wp-admin/options-general.php', function() {
               this.fill('form', {
                   blogname     : 'Psychological tests and quizes'
               }, true)
           }).waitForUrl(/updated/)

           casper.thenOpen('http://wpti.dev/wp-admin/options-permalink.php', function() {
               this.click('#permalink_structure')
               this.sendKeys('#permalink_structure', '/%postname%/')
               this.click('#submit')
           }).waitForUrl(/options/)

           casper.thenOpen('http://wpti.dev/wp-admin/edit.php?post_type=wpt_test', function() {
               this.evaluate(function() {
                   var ids = '#categories-hide,#tags-hide,#taxonomy-wpt_category-hide,#comments-hide';
                   jQuery(ids).attr('checked', false).click().click()
               })

               this.evaluate(function() {
                   return jQuery('body.folded').length > 0
               }) && this.clickLabel('Collapse menu') && this.wait(400)
           })
       }
   }, {
       title   : 'There are fast access buttons like "Add New Questions" at the top of the page. Test page and results page can be customized from sidebar',
       actions : function () {
           casper.thenOpen('http://wpti.dev/wp-admin/edit.php?post_type=wpt_test', function() {
               this.evaluate(function() {
                   return jQuery('body.folded').length > 0
               }) || this.clickLabel('Collapse menu') && this.wait(400)
               this.clickLabel('Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)')
           }).waitForUrl(/action=edit/)

           casper.then(function() {
               try {
                   this.clickLabel('Dismiss')
               } catch(e) {}
               this.evaluate(function() {
                   switchEditors.switchto(jQuery('#content-html')[0])
               })
               this.wait(400)
           })
       }
   }, {
       title   : 'Here we can see "Edit Questions and Scores" box where every scale has a sum of scores. Also we can add to each question individual answers. The choise of answers and scales is available in the sidebar. They can be reordered by drag-n-drop.',
       offset  : 1050,
       actions : function () {
           casper.then(function() {
               this.evaluate(function() {
                   jQuery('#commentstatusdiv-hide').attr('checked', true).click()
               })
               this.wait(400)
               this.evaluate(function() {
                   jQuery('#commentsdiv-hide').attr('checked', true).click()
               })
               this.wait(400)
           })
       }
   }, {
       title   : 'The "Quick Fill Scores" box is opened that allows us quickly enter scores from the questions separated by commas. "Add Individual Answers" box also opened but it tells us to use "Test Answers" in case when answers are same',
       offset  : 1050,
       actions : function () {
           casper.then(function() {
               this.clickLabel('Quick Fill Scores')
               this.clickLabel('Add Individual Answers')
           })
       }
    }, {
        title   : 'Fast adding questions from text',
        offset  : 900,
        actions : function () {
            casper.thenOpen('http://wpti.dev/wp-admin/edit.php?post_type=wpt_test', function() {
                this.clickLabel('Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)')
            }).waitForUrl(/action=edit/)

            casper.then(function() {
                this.evaluate(function() {
                    return jQuery('#wpt_edit_questions.closed').length > 0
                }) || this.clickLabel('Edit Questions and Scores', 'span')
                this.clickLabel('Quick Fill From Text', 'a')
                this.sendKeys('#wpt_quick_fill_questions textarea', [
                    'Question 1',
                    '2. Question 2',
                    'Next question'
                ].join('\n'))
            })
        }
    }, {
        title   : 'Editing formulas',
        offset  : 900,
        actions : function () {
            casper.then(function() {
                this.click('#wpt_quick_fill_questions input[type=button]')
            })
        }
    }, {
        title   : 'The example of the test without  scores. Some answers are individual and some are individualized',
        offset  : 600,
        actions : function () {
            casper.thenOpen('http://wpti.dev/wp-admin/edit.php?post_type=wpt_test', function() {
                this.clickLabel('Simple Test With Scores')
            }).waitForUrl(/action=edit/)

            casper.then(function() {
                this.evaluate(function() {
                    return jQuery('#wpt_edit_questions.closed').length > 0
                }) && this.clickLabel('Edit Questions and Scores', 'span') && this.wait(400)
            })
        }
    }, {
        title   : 'Respondents’ test results in admin area. Test link will open test in edit more and view link allow to see test result',
        actions : function () {
            casper.thenOpen('http://wpti.dev/wp-admin/edit.php?post_type=wpt_test&page=wpt_test_respondents_results')
        }
    }, {
        title   : 'Ready test on the home page',
        actions : function () {
            casper.thenOpen('http://wpti.dev/wp-login.php?action=logout', function() {
                this.clickLabel('log out', 'a')
            }).waitForUrl(/loggedout/)

            casper.thenOpen('http://wpti.dev/')
        }
    }, {
        title   : 'The page with the description of the test, questions and answers',
        offset  : 1150,
        actions : function () {
            casper.then(function() {
                this.clickLabel('Eysenck’s Personality Inventory (EPI) (Extroversion/Introversion)')
            }).waitForUrl(/eysencks/)
        }
    }, {
        title   : 'The button is disabled until all questions are not answered',
        offset  : 7200,
        actions : function () {
        }
    }, {
        title   : 'Get test results after all questions are answered',
        offset  : 7200,
        actions : function () {
            casper.then(function() {
                for (var i = 1, iMax = 57; i <= iMax; i++) {
                    this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[' + i + ']/*//label')
                }
            })
        }
    }, {
        title   : 'The result page on it`s own URL contains both the result of the test and the scales that create a result',
        actions : function () {
            casper.then(function() {
                this.fill('form#wpt-test-form', {}, true)
            }).waitForUrl(/test.+eysencks/)
        }
    }, {
        title   : 'Scale description with "more..." text closed',
        offset  : 1500,
        actions : function () {
        }
    }, {
        title   : 'Scale description with "more..." text opened (after clicking on "more" link)',
        offset  : 1500,
        actions : function () {
            casper.then(function() {
                this.clickLabel('more…', 'a')
            })
        }
    }, {
        title   : 'A test without scores is shown like a "Test is under construction". Answers titles are those that was entered',
        actions : function () {
            casper.thenOpen('http://wpti.dev/')

            casper.then(function() {
                this.clickLabel('Simple Test With Scores')
            }).waitForUrl(/simple/)
        }
    }, {
        title   : 'Test results with scales chart. Hovered scale shows it`s value and title in dynamic tag',
        actions : function () {
            casper.thenOpen('http://wpti.dev/test/diagram-with-same-length-scales/').waitForUrl(/diagram-with-same/, function() {
                this.clickLabel('Yes', '*[@id="wpt-test-form"]/*//label')
                this.fill('form#wpt-test-form', {}, true)
            }).waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function () {
                this.mouse.move('.scales.diagram')
            })
        }
    }, {
        title   : 'In case when scales has different length (possible max total) they are shown as percents',
        actions : function () {
            casper.thenOpen('http://wpti.dev/test/diagram-with-different-length-scales-uses-percents/').waitForUrl(/diagram-with-different/, function() {
                this.clickLabel('Yes', '*[@id="wpt-test-form"]/*//label')
                this.fill('form#wpt-test-form', {}, true)
            }).waitForUrl(/test.+[a-z0-9]+[a-f0-9]{32}/, function () {
                this.mouse.move('.scales.diagram')
            })
        }
    }, {
        title   : 'Multiple answers per question are also possible',
        actions : function () {
            casper.thenOpen('http://wpti.dev/?wpt_test=multiple-answers').waitForUrl(/multiple-answers/)

            casper.then(function() {
                this.clickLabel('Yes', '*[@id="wpt-test-form"]/*[1]/*//label')
                this.clickLabel('No',  '*[@id="wpt-test-form"]/*[2]/*//label')
            })
        }
    }
];

casper.each(screenshots, function(self, screenshot, index) {
    screenshot.actions()

    casper.then(function() {
        var options  = this.options.viewportSize
        options.top  = screenshot.offset || 0
        options.left = 0
        screenIndex  = ('0' + (index + 1)).slice(-2)

        this.evaluate(function(offsetTop) {
            var divHeight = 25;
            var urlDiv    = jQuery('#url-element');
            if (urlDiv.length == 0) {
                urlDiv = jQuery('<div/>').attr('id', 'url-element').css({
                    position    : 'absolute',
                    height      : divHeight,
                    left        : 0,
                    background  : 'gainsboro',
                    color       : 'black',
                    padding     : '2px 10px 0px 2px',
                    borderTopRightRadius: '10px',
                    fontFamily  : 'sans-serif',
                    fontSize    : '14px',
                    opacity     : 0.8
                }).appendTo('body')
            }
            urlDiv.text(document.location).css({top: offsetTop - divHeight})
        }, options.height + options.top)

        this.capture('screenshot-' + screenIndex + '.png', options)
        this.capture(screenIndex + '-' + screenshot.title + '.png', options)
        this.echo(screenIndex + '. ' + screenshot.title)
    })

})

casper.run(function() {
    this.exit()
})