#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

# Define vars
HERE=$(dirname $(realpath $0))
REPO_ROOT=$(realpath $HERE/../..)

# Prepare environment
cd $REPO_ROOT
sudo WP_T_SERVER=http://wpti.dev WP_VERSION=$WP_VERSION tests/integration-environment/create.sh
sudo chown --recursive www-data:www-data /tmp/wpti/wordpress
cd $HERE
export PATH=$PATH:./node_modules/.bin/
export TZ="UTC"
export WP_T_MULTISITE=1

# Reset shared cookies
rm -f /tmp/cookies.*.txt

# Activate plugin and setup admin
export WP_T_MULTI_SERVER=http://wpti.dev
export WP_T_SERVER=http://after.wpti.dev
mocha-casperjs --timeout=360000
