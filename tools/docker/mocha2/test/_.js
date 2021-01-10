require('chai').should();
const puppeteer = require('puppeteer-core');

/** @type puppeteer.Browser */
let browser;

module.exports.mochaHooks = {
  afterAll: () => {
    browser.disconnect();
  },
};

module.exports.page = async () => {
  if (typeof browser === 'undefined') {
    browser = await puppeteer.connect({
      browserURL: 'http://localhost:9222/',
      defaultViewport: null,
      //    slowMo: 10,
    });
  }

  // (await browser.pages()).forEach(async (page) => { await page.close(); });

  return browser.newPage();
};

module.exports.adminPage = async () => {
  const page = await this.page();

  await Promise.all([
    page.goto('http://wpt.docker/wp-login.php'),
    page.waitForNavigation(),
  ]);

  await page.click('input[name=log]', { clickCount: 3 });
  await page.type('input[name=log]', 'wpti');
  await page.type('input[name=pwd]', 'wpti');
  await Promise.all([
    page.click('input[type=submit]'),
    page.waitForNavigation(),
  ]);

  (await page.content()).should.contains('Dashboard');

  return page;
};
