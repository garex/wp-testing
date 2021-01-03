const puppeteer = require('puppeteer-core');

module.exports.page = async () => {
  const browser = await puppeteer.connect({
    browserURL: 'http://chrome.localhost:9222/',
    defaultViewport: {
      width: 1280,
      height: 720,
      hasTouch: false,
      isMobile: false,
      isLandscape: true,
    },
  });

  (await browser.pages()).forEach(async (page) => { await page.close(); });

  return browser.newPage();
};

module.exports.adminPage = async () => {
  const page = await this.page();

  await page.goto('http://wpt.docker/wp-login.php');

  await page.type('input[name=log]', 'wpti');
  await page.type('input[name=pwd]', 'wpti');

  await page.click('input[type=submit]');

  await page.waitForNavigation();

  return page;
};
