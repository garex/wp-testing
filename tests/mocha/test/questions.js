describe('Questions', function() {

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

    it('should be added to new test', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Fatal'.should.not.be.textInDOM
            'Add New Test'.should.be.inTitle

            this.fill('form#post', {
                'post_title'                                    : 'To Be or Not to Be?',
                'wp_testing_model_questions::question_title[0]' : 'To Be?',
                'wp_testing_model_questions::question_title[5]' : 'Not to Be?'
            })
            this.click('#publish')
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            expect('wp_testing_model_questions::question_title[0]').to.have.fieldValue('To Be?')
            expect('wp_testing_model_questions::question_title[1]').to.have.fieldValue('Not to Be?')
            expect('wp_testing_model_questions::question_title[2]').to.have.fieldValue('')
        })
    })

    it('should be removed and updated in test', function() {
        casper.then(function() {
            this.clickLabel('All Tests', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            this.clickLabel('To Be or Not to Be?', 'a')
        })

        casper.then(function() {
            this.clickLabel(' Lie', 'label')

            this.fill('form#post', {
                'wp_testing_model_questions::question_title[0]' : '',
                'wp_testing_model_questions::question_title[1]' : 'Not to Be???',
                'wp_testing_model_questions::question_title[2]' : 'But Why?'
            })
            this.fill('form#post', {}, true)
        })

        casper.waitForUrl(/message/, function() {
            'Fatal'.should.not.be.textInDOM
            '#message'.should.be.inDOM
            expect('wp_testing_model_questions::question_title[0]').to.have.fieldValue('Not to Be???')
            expect('wp_testing_model_questions::question_title[1]').to.have.fieldValue('But Why?')
            expect('wp_testing_model_questions::question_title[2]').to.have.fieldValue('')
        })
    })

    it('should be then shown in test', function() {
        casper.evaluate(function() {
            document.location = jQuery('#view-post-btn a').attr('href')
        })

        casper.waitForUrl(/wpt_test/, function() {
            'Fatal'.should.not.be.textInDOM
            '.wpt_test.fill_form'.should.be.inDOM
            'document.querySelectorAll(".wpt_test.fill_form .question").length'.should.evaluate.to.equal(2)
        })
    })
})
