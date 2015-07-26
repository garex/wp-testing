#!/usr/bin/env bash

HERE=$(readlink -f $(dirname "$0"))
PUBLIC=/tmp/wpti/wordpress/wp-content/plugins/wp-testing/tools/screenshots
rm -rf $PUBLIC
ln -s $HERE $PUBLIC
cd "$HERE"
rm -rf raw/*.png decorated/*.png
PHANTOMJS_EXECUTABLE=node_modules/casperjs/node_modules/.bin/phantomjs node_modules/.bin/casperjs create-screenshots.js
