describe('Test', function() {

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

    it('should be created without questions, answers and taxonomies', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.evaluate(function() {
                jQuery('#post-body-content a').filter(':contains("Visual")').prev().addClass('__text_tab_here')
            })
            this.click('a.__text_tab_here')

            this.fill('form#post', {
                'post_title' : 'Are You Hot or Not?',
                'content'    : 'Allow others to rate the vacuum on the Earth'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            expect('post_title').to.have.fieldValue('Are You Hot or Not?')
            expect('content').to.have.fieldValue('Allow others to rate the vacuum on the Earth')
        })
    })

    it('should be updated', function() {
        casper.then(function() {
            this.fill('form#post', {
                'post_title' : 'Are You Hot or Not?!',
                'content'    : 'Allow others to rate the vacuum on the Earth!'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            expect('post_title').to.have.fieldValue('Are You Hot or Not?!')
            expect('content').to.have.fieldValue('Allow others to rate the vacuum on the Earth!')
        })
    })

    it('should be in [wptlist] shortcode after publish', function() {
        casper.thenOpen('http://wpti.dev/', function() {
            '.wp-testing.shortcode.list'.should.be.inDOM
            '.wp-testing.shortcode.list li'.should.not.contain.text('Hot or Not?!')
        })

        casper.thenOpen('http://wpti.dev/wp-admin/', function() {
            this.clickLabel('All Tests', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.clickLabel('Are You Hot or Not?!', 'a')
        })

        casper.then(function() {
            'Edit Test'.should.be.inTitle
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
        })

        casper.thenOpen('http://wpti.dev/', function() {
            '.wp-testing.shortcode.list'.should.be.inDOM
            '.wp-testing.shortcode.list li'.should.contain.text('Hot or Not?!')
        })
    })

})
