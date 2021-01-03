require('chai').should();
const puppeteer = require('puppeteer-core');

describe('WordPress', () => {
  it('should be installed', async () => {
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

    const page = await browser.newPage();

    await page.goto('http://wpt.docker/');

    const title = await page.title();

    title.should.contain('wpti');

    page.close();
    page.browser.disconnect();
  });
});
