require('chai').should();
const puppeteer = require('puppeteer-core'); // eslint-disable-line no-unused-vars
const _ = require('./_');

describe('Admin', () => {
  /** @type puppeteer.Page */
  let page;

  it('should disable visual editing', async function () { // eslint-disable-line func-names
    page = await _.adminPage();

    await Promise.all([
      page.goto('http://wpt.docker/wp-admin/profile.php'),
      page.waitForNavigation(),
    ]);

    const richEditDisabledSelector = 'input[name=rich_editing]:not(:checked)';
    if ((await page.$(richEditDisabledSelector)) == null) {
      this.skip();
    }

    await page.click(richEditDisabledSelector);

    await Promise.all([
      page.click('#submit'),
      page.waitForNavigation(),
      page.waitForResponse((response) => response.url().includes('updated')),
    ]);
  });

//  describe('Create default user in subscriber role', () => {
//    it('should fill new user form', () => {
//      casper.thenOpen(`${server}/wp-admin/user-new.php`, function () {
//        this.evaluate(() => {
//          $ = jQuery;
//          $('#user_login').val('user');
//          $('#email').val('user@wpti.dev');
//          $('#pass1').data('pw', 'user').val('user');
//          $('#pass1-text').val('user');
//          $('#pass2').val('user');
//          $('#noconfirmation').attr('checked', true);
//        });
//      });
//    });
//
//    it('should submit form and check that user added', () => {
//      casper.then(function () {
//        this.evaluate(() => {
//          $('#createuser').submit();
//        });
//      });
//
//      casper.waitForUrl(/update/, () => {
//        if (multisite) {
//          'User has been added to your site.'.should.be.textInDOM;
//        } else {
//          'user@wpti.dev'.should.be.textInDOM;
//        }
//      });
//    });
//
//    if (!multisite) {
//      return;
//    }
//
//    it('should change user password to known one', () => {
//      casper.thenOpen(`${server}/wp-admin/user-edit.php?user_id=2`, function () {
//        this.evaluate(() => {
//          $ = jQuery;
//          $('#pass1').val('user');
//          $('#pass2').val('user');
//          $('#submit').click();
//        });
//      });
//
//      casper.waitForUrl(/updated/, () => {
//        'User updated.'.should.be.textInDOM;
//      });
//    });
//  });
});
