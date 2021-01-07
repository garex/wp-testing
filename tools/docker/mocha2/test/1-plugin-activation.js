require('chai').should();
const puppeteer = require('puppeteer-core'); // eslint-disable-line no-unused-vars
const _ = require('./_');

describe('Plugin activation', () => {
  /** @type puppeteer.Page */
  let page;

  it('should open plugins page', async () => {
    page = await _.adminPage();

    await page.goto('http://wpt.docker/wp-admin/plugins.php');

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
});
