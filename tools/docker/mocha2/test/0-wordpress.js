require('chai').should();

const _ = require('./_');

describe('WordPress', () => {
  it('should be installed', async () => {
    const page = await _.page();

    await page.goto('http://wpt.docker/');

    const title = await page.title();

    title.should.contain('wpti');

    //    page.close();
    page.browser().disconnect();
  });
});
