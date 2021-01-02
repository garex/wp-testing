require('chai').should();
const puppeteer = require('puppeteer-core'); // eslint-disable-line no-unused-vars
const _ = require('./_');

describe('Plugin activation', () => {
  /** @type puppeteer.Page */
  let page;

  it('should open plugins page', async () => {
    page = await _.adminPage();

    await Promise.all([
      page.goto('http://wpt.docker/wp-admin/plugins.php'),
      page.waitForNavigation(),
    ]);

    (await page.$eval('body', (body) => body.innerText)).should.contains('Wp-testing');
  });

  it('should activate main plugin and others', async function () { // eslint-disable-line func-names
    const deactivateSelector = '#wp-testing .deactivate a,[data-slug=wp-testing] .deactivate a';
    if ((await page.$(deactivateSelector)) !== null) {
      this.skip();
    }

    await page.click('#cb input');
    await page.select('.wrap form select:first-of-type', 'activate-selected');

    await Promise.all([
      page.click('#doaction'),
      page.waitForNavigation(),
      page.waitForResponse((response) => response.url().includes('activate')),
    ]);

    const el = await page.$(deactivateSelector);
    el.should.be.not.null;
  });

  it('should have paid links', async () => {
    (await page.$eval('body', (body) => body.innerText)).should.contains('Paid add-ons | Paid support');
  });

  it('should have paid addons link in menu', async () => {
    const el = await page.$('a[href$=wpt_feedback_paid_addons]');
    el.should.be.not.null;
  });

  it('should opens remote url', async () => {
    await Promise.all([
      page.goto('http://wpt.docker/wp-admin/edit.php?post_type=wpt_test&page=wpt_feedback_paid_addons', { waitUntil: 'domcontentloaded' }),
      page.waitForRequest((response) => response.url().includes('spreadsheets')),
    ]);
  });
});
