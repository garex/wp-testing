#!/usr/bin/env bash

source /etc/profile.d/phpenv.sh

if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
then
    phpenv shell 5.3
    composer self-update
    composer update --no-interaction
    phpenv shell --unset
else
    composer self-update
    composer update --no-interaction
fi
