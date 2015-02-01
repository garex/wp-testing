#!/usr/bin/env bash

source /etc/profile.d/phpenv.sh

if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
then
    phpenv shell 5.3
    composer selfupdate 1.0.0-alpha9
    composer update --no-interaction
    phpenv shell --unset
else
    composer selfupdate 1.0.0-alpha9
    composer update --no-interaction
fi
