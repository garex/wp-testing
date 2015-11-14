var casper = require('casper').create({
    verbose: true,
    logLevel: "debug"
});

casper.options.viewportSize = {width: 1280, height: 850}

casper
.start('http://wpti.dev:8000/')

.thenOpen('http://wpti.dev:8000/wp-login.php', {
    method: 'post',
    data  : {
        log: 'wpti',
        pwd: 'wpti'
    }
})

.thenOpen('http://wpti.dev:8000/wp-admin/profile.php', function() {
    this.clickLabel('Blue', 'label')
    this.fill('form#your-profile', {
        nickname     : 'Tests Author',
        display_name : 'Tests Author',
        email        : 'ustimenko.alexander@gmail.com'
    }, true)
}).waitForUrl(/updated/)

.thenOpen('http://wpti.dev:8000/wp-admin/options-general.php', function() {
    this.fill('form', {
        blogname        : 'Psychological tests and quizzes',
        blogdescription : 'WordPress testing plugin'
    }, true)
}).waitForUrl(/updated/)

.thenOpen('http://wpti.dev:8000/wp-admin/options-permalink.php', function() {
    this.click('#permalink_structure')
    this.sendKeys('#permalink_structure', '/%postname%/')
    this.click('#submit')
}).waitForUrl(/options/)

.thenOpen('http://wpti.dev:8000/wp-admin/customize.php?theme=twentyfifteen', function() {
    this.evaluate(function() {
        function changeColor(name, value) {
            jQuery('#customize-control-' + name + ' .wp-color-picker')
                .val(value)
                .change();
        };

        changeColor('background_color',         '#e0e046');
        changeColor('sidebar_textcolor',        '#1B380F');
        changeColor('header_background_color',  '#b8f741');

    })

    this.click('#save')
}).waitForText('Saved')

.thenOpen('http://wpti.dev:8000/wp-admin/plugins.php', function () {
    this.click('#cb input')
    this.evaluate(function() {
        jQuery('.wrap form select:first').val('activate-selected')
    })
    this.click('#doaction')
}).waitForUrl(/activate/, null, null, 60000)

.then(function() {
    this.click('#cb input')
    this.evaluate(function() {
        jQuery('.wrap form select:first').val('deactivate-selected')
    })
    this.click('#doaction')
}).waitForUrl(/deactivate/)

casper.run(function() {
    this.exit()
})
