require('chai').should();
const _ = require('./_');

describe('WordPress', () => {
  it('should be installed', async () => {
    const page = await _.page();

    await page.goto('http://wpt.docker/');

    (await page.title()).should.contain('wpti');
  });
});
