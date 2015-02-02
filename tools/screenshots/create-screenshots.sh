#!/usr/bin/env bash

rm -rf *.png
PHANTOMJS_EXECUTABLE=node_modules/casperjs/node_modules/.bin/phantomjs node_modules/.bin/casperjs create-screenshots.js
