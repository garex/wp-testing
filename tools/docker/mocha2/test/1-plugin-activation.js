require('chai').should();
const _ = require('./_');

describe('Plugin activation', () => {
  it('should open plugins page', async () => {
    const page = await _.adminPage();

    await page.goto('http://wpt.docker/wp-admin/plugins.php');

    (await page.$eval('body', (body) => body.innerText)).should.contains('Wp-testing');
  });
});
