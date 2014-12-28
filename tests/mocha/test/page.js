describe('Page', function() {

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

    it('should not disappear when plugin activated', function() {
        casper.thenOpen('http://wpti.dev/wp-admin/post-new.php?post_type=page', function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Page'.should.be.inTitle

            this.evaluate(function() {
                jQuery('#edButtonHTML,#content-html').addClass('__text_tab_here')
            })
            this.click('.__text_tab_here')

            this.fill('form#post', {
                'post_title' : 'Simple Page That Not Disappear!',
                'content'    : 'Because some plugin have bug somedays ago.'
            })

            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM

            this.evaluate(function() {
                document.location = jQuery('#view-post-btn a').attr('href')
            })
        })

        casper.waitForUrl(/page_id/, function() {
            'Fatal'.should.not.be.textInDOM
            'Disappear'.should.be.textInDOM
            'error404'.should.not.be.textInDOM
        })
    })
})
