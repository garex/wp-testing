#!/usr/bin/env bash

set -e

HERE=$(readlink -f $(dirname "$0"))

cd $HERE/../..

tests/integration-environment/create.sh

cd $HERE
PHANTOMJS_EXECUTABLE=node_modules/casperjs/node_modules/.bin/phantomjs node_modules/.bin/casperjs setup-site.js
