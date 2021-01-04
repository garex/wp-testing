require('chai').should();

const _ = require('./_');

describe('WordPress', () => {
  it('should be installed', async () => {
    const page = await _.page();

    try {
      await page.goto('http://wpt.docker/');

      const title = await page.title();

      title.should.contain('wpti');
      return Promise.resolve();
    } catch (e) {
      return Promise.reject(e);
    } finally {
      page.browser().disconnect();
    }
  });
});
