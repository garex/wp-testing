#!/usr/bin/env bash

notify-send --icon=non-starred --urgency=critical "Starting screenshots..."
HERE=$(readlink -f $(dirname "$0"))
PLUGIN=/tmp/wpti/wordpress/wp-content/plugins/wp-testing
PUBLIC=$PLUGIN/tools/screenshots

if [[ ! -L $PLUGIN ]]
then
    rm -rf $PUBLIC
    ln -s $HERE $PUBLIC
fi

cd "$HERE"
rm -rf raw/*.png decorated/*.png
PHANTOMJS_EXECUTABLE=node_modules/casperjs/node_modules/.bin/phantomjs node_modules/.bin/casperjs create-screenshots.js
notify-send --icon=starred --urgency=critical "Screenshots done"
