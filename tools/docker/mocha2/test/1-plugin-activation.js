require('chai').should();

const _ = require('./_');

describe('Plugin activation', () => {
  it('should open plugins page', async () => {
    const page = await _.adminPage();

    await page.goto('http://wpt.docker/wp-admin/plugins.php');

    (await page.content()).should.contains('Plugins');

    page.close();
    page.browser().disconnect();
  });
});
