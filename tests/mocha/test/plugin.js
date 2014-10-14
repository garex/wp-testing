var childProcess = require('child_process');

describe('Plugin', function() {

    before(function (done) {
        this.timeout(3600000)
        console.log()
        console.log('    Creating environment')
        childProcess.execFile('/bin/bash', ['../integration-environment/create.sh'], null, function (err, stdout, stderr) {
            console.log('    ' + stdout.replace(/\n/g, '\n    '))
            casper.start('http://wpti.dev/wp-admin/').thenOpen('http://wpti.dev/wp-login.php', {
                method: 'post',
                data  : {
                    log: 'wpti',
                    pwd: 'wpti'
                }
            })
            done()
        });
    });

    it('should be activated', function() {
       casper.thenOpen('http://wpti.dev/wp-admin/plugins.php', function () {
           expect(/Plugins/).to.matchTitle
           '.plugin-title'.should.contain.text('Wp-testing')
           '#wp-testing .activate a'.should.be.inDOM
           this.click('#wp-testing .activate a')
       })

       casper.then(function() {
           '#wp-testing .deactivate a'.should.be.inDOM
       })
    })

    it('should be deactivated', function() {
       casper.then(function() {
           this.click('#wp-testing .deactivate a')
       })

       casper.then(function() {
           '#wp-testing .activate a'.should.be.inDOM
       })
    })

    it('should be deleted', function() {
       casper.then(function() {
           this.click('#wp-testing .delete a')
       })

       casper.then(function() {
           this.click('#submit')
       })

       casper.then(function() {
           '#wp-testing'.should.not.be.inDOM
       })
    })

})
