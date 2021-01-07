require('chai').should();
const puppeteer = require('puppeteer-core');

let browser;

module.exports.mochaHooks = {
  afterEach: () => {
    browser.disconnect();
  },
};

module.exports.page = async () => {
  browser = await puppeteer.connect({
    browserURL: 'http://localhost:9222/',
    defaultViewport: null,
    slowMo: 10,
  });

  // (await browser.pages()).forEach(async (page) => { await page.close(); });

  return browser.newPage();
};

module.exports.adminPage = async () => {
  const page = await this.page();

  await page.goto('http://wpt.docker/wp-login.php');

  await page.click('input[name=log]', { clickCount: 3 });
  await page.type('input[name=log]', 'wpti');
  await page.type('input[name=pwd]', 'wpti');

  await page.evaluate(() => { debugger; }); // eslint-disable-line no-debugger

  await page.click('input[type=submit]');

  //  await page.waitForNavigation();

  (await page.content()).should.contains('Dashboard');

  return page;
};
