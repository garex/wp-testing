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

    it('should allow to edit scales and use HTML inside', function() {
        var label = 'Temperature'

        casper.then(function() {
            this.clickLabel('Scales', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Scales'.should.be.inTitle

            this.fill('form#addtag', {
                'tag-name'    : label,
                'description' : 'A temperature is a comparative <img class="wow" src="cool" /> objective measure of hot and cold.'
            }, true)

            this.waitForText(label, function() {
                this.clickLabel(label, 'a')
            })
        })

        casper.then(function() {
            'description.value'.should.evaluate.to.be.equal('A temperature is a comparative <img class="wow" src="cool" /> objective measure of hot and cold.')

            this.fill('form#edittag', {
                'description' : '<h2>What?</h2> A temperature is a comparative <img class="wow" src="cool" /> objective measure of hot and cold.'
            }, true)

            this.waitForUrl(/message/, function() {
                this.clickLabel(label, 'a')
            })
        })

        casper.then(function() {
            'description.value'.should.evaluate.to.be.equal('<h2>What?</h2> A temperature is a comparative <img class="wow" src="cool" /> objective measure of hot and cold.')
        })
    })

    it('should allow to use read more tag', function() {
        casper.then(function() {
            this.clickLabel('Scales', '*[@id="menu-posts-wpt_test"]/*//a')
        })

        casper.then(function() {
            'Scales'.should.be.inTitle

            this.clickLabel('Lie', 'a')
        })

        casper.then(function() {
            this.fill('form#edittag', {
                'description' : 'Lie is bad<!--more-->\nIt measures how socially desirable you are trying to be in your answers. Those who score 5 or more on this scale are probably trying to make themselves look good and are not being totally honest in their responses.'
            }, true)

            this.waitForUrl(/message/, function() {
                this.clickLabel('Lie', 'a')
            })
        })

        casper.then(function() {
            'description.value'.should.evaluate.to.be.equal('Lie is bad<!--more-->\nIt measures how socially desirable you are trying to be in your answers. Those who score 5 or more on this scale are probably trying to make themselves look good and are not being totally honest in their responses.')
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
