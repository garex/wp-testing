#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

function kill_php_cgi {
    [ ! -f /etc/profile.d/phpenv.sh ] && return 0

    if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
    then
        ps ax | grep php-cgi | grep -v grep | sed 's/^ *//' | cut -f1 -d" " | sudo xargs kill
    fi
}

function php_cgi {
    [ ! -f /etc/profile.d/phpenv.sh ] && return 0

    if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
    then
        PHP_52=$(phpenv version-name)
        PHP_CGI=$(realpath ~/.phpenv/versions/$PHP_52/bin/php-cgi)
        USER=www-data PHP_FCGI_CHILDREN=15 PHP_FCGI_MAX_REQUESTS=1000 sudo $PHP_CGI --bindpath 127.0.0.1:9000 &
    fi
}

function realpath {
    local path=$1
    php -r 'echo realpath($argv[1]);' $path
}

function main {
    kill_php_cgi
    php_cgi
}

main
