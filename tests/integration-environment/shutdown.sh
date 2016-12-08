#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
then
    killall php-cgi
fi
