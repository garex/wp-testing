require('chai').should();

const _ = require('./_');

describe('WordPress', () => {
  it('should be installed', async (done) => {
    const page = await _.page();

    try {
      await page.goto('http://wpt.docker/');

      const title = await page.title();

      title.should.contain('wpti');
    } catch (e) {
      done(e);
    } finally {
      page.browser().disconnect();
    }
  });
});
