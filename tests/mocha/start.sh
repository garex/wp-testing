#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Define vars
HERE=$(dirname $(realpath $0))
TRAVIS_BUILD_DIR=$(realpath $HERE/../..)

# Prepare environment
cd $TRAVIS_BUILD_DIR
tests/integration-environment/create.sh
cd $TRAVIS_BUILD_DIR/tests/mocha
npm install
cd $TRAVIS_BUILD_DIR
export PATH=$PATH:$TRAVIS_BUILD_DIR/tests/mocha/node_modules/.bin/
export TZ="UTC"

# Run tests
cd $TRAVIS_BUILD_DIR/tests/mocha
mocha-casperjs --grep='Plugin:' --invert
