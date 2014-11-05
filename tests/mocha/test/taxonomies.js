describe('Taxonomies', function() {

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

    it('tests menu should exists', function() {
        casper.thenOpen('http://wpti.dev/wp-admin/', function() {
            '#menu-posts-wpt_test'.should.be.inDOM
        })
    })

    it('should allow to edit answers', function() {
        casper.then(function() {
            this.clickLabel('Answers', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Answers'.should.be.inTitle

            this.fill('form#addtag', {
                'tag-name': 'Of course'
            }, true)

            this.waitForText('of-course')
        })
    })

    it('should allow to edit scales', function() {
        casper.then(function() {
            this.clickLabel('Scales', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Scales'.should.be.inTitle

            this.fill('form#addtag', {
                'tag-name'    : 'Temperature',
                'description' : 'A temperature is a comparative objective measure of hot and cold.'
            }, true)

            this.waitForText('measure of hot and cold')
        })
    })

    it('should allow to edit results', function() {
        casper.then(function() {
            this.clickLabel('Results', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Results'.should.be.inTitle

            this.fill('form#addtag', {
                'tag-name'    : 'Hot',
                'description' : 'Sun'
            }, true)

            this.waitForText('Sun')

            this.fill('form#addtag', {
                'tag-name'    : 'Cold',
                'description' : 'Ice'
            }, true)

            this.waitForText('Ice')
        })
    })

    it('should then allow to select answers, scales and results in test form', function() {
        casper.then(function() {
            this.clickLabel('Add New', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Of course'.should.be.textInDOM
            'Temperature'.should.be.textInDOM
            'Hot'.should.be.textInDOM
            'Cold'.should.be.textInDOM
        })
    })

})
