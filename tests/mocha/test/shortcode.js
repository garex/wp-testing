describe('Shortcode', function() {

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

    it('[wptlist] should be added', function() {
        casper.thenOpen('http://wpti.dev/wp-admin/post.php?post=1&action=edit', function() {
            this.evaluateOrDie(function() {
                return /Edit Post/.test(document.body.innerText)
            })

            this.evaluate(function() {
                jQuery('#edButtonHTML,#content-html').addClass('__text_tab_here')
            })

            this.click('.__text_tab_here')

            this.fillSelectors('form#post', {
                '#title'   : 'Hi World!',
                '#content' : 'Hello World!\n[wptlist]'
            }, true)
        })

        casper.waitForUrl(/message/, function() {
            '#message'.should.be.inDOM
        })

        casper.thenOpen('http://wpti.dev/?p=1', function() {
            '.wp-testing.shortcode.list'.should.be.inDOM
            '.wp-testing.shortcode.list li'.should.contain.text('EPI')
        })
    })

})
