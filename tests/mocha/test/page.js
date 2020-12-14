describe('Page', function() {

    var env = require('../env'),
        server = env.server(),
        isWp5 = env.isWp5Already(),
        isWp53 = env.isWp53Already(),
        isWp54 = env.isWp54Already()

    before(function () {
        require('../login-as').admin(this)
    })

    it('should not disappear when plugin activated', function() {
        casper.thenOpen(server + '/wp-admin/post-new.php?post_type=page', function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Page'.should.be.inTitle

            if (isWp54) {
                this.evaluate(function () {
                    if (wp.data.select('core/edit-post').isFeatureActive('welcomeGuide')) {
                        wp.data.dispatch('core/edit-post').toggleFeature('welcomeGuide');
                    }
                })
            } else if (isWp53) {
                this.evaluate(function () {
                    // https://wordpress.stackexchange.com/questions/334559/deactivate-gutenberg-tips-forever-not-gutenberg
                    wp.data.dispatch('core/nux').disableTips();
                })
            }
            if (isWp5) {
                return;
            }

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

        if (!isWp5) {
            casper.waitWhileSelector('form#post.wpt-ajax-save').waitForUrl(/message/, function() {
                'Fatal'.should.not.be.textInDOM
                '#message'.should.be.inDOM

                this.evaluate(function() {
                    document.location = jQuery('#view-post-btn a,#post-preview').attr('href')
                })
            })
        } else {
            casper.waitForSelector('#post-title-0', function() {
                this.sendKeys('#post-title-0', 'Simple Page That Not Disappear!');
                this.evaluate(function(coreEditor) {
                    wp.data.dispatch(coreEditor).insertBlocks(wp.blocks.createBlock('core/paragraph', {content: 'Because some plugin have bug somedays ago.'}));
                }, isWp53 ? 'core/block-editor' : 'core/editor')

                this.click('.editor-post-publish-panel__toggle')
                this.click('.editor-post-publish-button')
            })

            if (isWp53) {
                casper.waitForSelector('.is-opened .post-publish-panel__postpublish-buttons').evaluate(function () {
                    document.location = jQuery('.is-opened .post-publish-panel__postpublish-buttons a:first').attr('href')
                })

                return
            }
            casper.waitForSelector('.components-button.components-notice__action.is-link', function() {
                'Fatal'.should.not.be.textInDOM
                '#message,.components-notice__content'.should.be.inDOM

                this.evaluate(function() {
                    document.location = jQuery('#view-post-btn a,#post-preview,.components-button.components-notice__action.is-link').attr('href')
                })
            })
        }

        casper.waitForUrl(/page_id|disappear/, function() {
            'Fatal'.should.not.be.textInDOM
            'Disappear'.should.be.textInDOM
            'error404'.should.not.be.textInDOM
        })
    })

    it('should be searchable', function() {
        casper.thenOpen(server + '/?s=Disappear', function() {
            'Fatal'.should.not.be.textInDOM
            'Sorry'.should.not.be.textInDOM
            'Simple Page That Not Disappear!'.should.be.textInDOM
        })
    })
})
