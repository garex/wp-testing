#!/usr/bin/env bash

notify-send --icon=non-starred --urgency=critical "Starting screenshots..."
HERE=$(readlink -f $(dirname "$0"))
PLUGIN=/tmp/wpti/wordpress/wp-content/plugins/wp-testing
MUST_USE_PLUGINS=/tmp/wpti/wordpress/wp-content/mu-plugins
PUBLIC=$PLUGIN/tools/screenshots

if [[ ! -L $PLUGIN ]]
then
    rm -rf $PUBLIC
    ln -s $HERE $PUBLIC
fi

# Remove rate us option
mysql --execute 'delete from wpti.wp_options where option_name="wpt_rateus_clicked"'

# Fix tinymce URL unavailability under phantom
mkdir --parents $MUST_USE_PLUGINS
ln --symbolic --force $HERE/must-use-tinymce-fix.php $MUST_USE_PLUGINS

cd "$HERE"
rm -rf raw/*.png decorated/*.png
PHANTOMJS_EXECUTABLE=node_modules/casperjs/node_modules/.bin/phantomjs node_modules/.bin/casperjs create-screenshots.js
notify-send --icon=starred --urgency=critical "Screenshots done"
