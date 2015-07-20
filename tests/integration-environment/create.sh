#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

HERE=$(dirname $0)
DB_ENGINE=${DB_ENGINE:-InnoDB}
DB_CHARSET=${DB_CHARSET:-utf8}
WP_VERSION=${WP_VERSION:-4.2.2}
WP_UPGRADE=${WP_UPGRADE:-0}
PLUGINS=${PLUGINS:-}

function init {
    log 'Define vars'
    HERE=$(dirname $(realpath $0))
}


function db {
    log 'Creating DB and user'
    mysql --user=root --execute='
        DROP DATABASE IF EXISTS wpti;
        CREATE DATABASE wpti DEFAULT CHARACTER SET '$DB_CHARSET';

        GRANT USAGE ON wpti.* TO wpti;
        DROP USER wpti;

        CREATE USER wpti IDENTIFIED BY "wpti";
        GRANT ALL ON wpti.* TO wpti;
    '
}


function setup_link {
    log 'Setting up symbolic link'
    rm --force /tmp/wpti
    ln --symbolic $HERE /tmp/wpti
}

function start_nginx {
    log 'Configuring and reloading nginx'
    ps ax | grep "[n]ginx -g" && nginx -g "error_log /tmp/wpti/error.log;" -c /tmp/wpti/nginx.conf -s stop
    nginx -g "error_log /tmp/wpti/error.log;" -c /tmp/wpti/nginx.conf
}

function php_cgi {
    [ ! -f /etc/profile.d/phpenv.sh ] && return 0

    log 'Configuring php cgi'
    source /etc/profile.d/phpenv.sh
    echo "cgi.fix_pathinfo = 1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
    if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
    then
        PHP_52=$(phpenv version-name)
        PHP_CGI=$(realpath ~/.phpenv/versions/$PHP_52/bin/php-cgi)
        USER=www-data PHP_FCGI_CHILDREN=15 PHP_FCGI_MAX_REQUESTS=1000 $PHP_CGI --bindpath 127.0.0.1:9000 &
    else
        cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
        echo 'user  = www-data' | tee --append ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf > /dev/null
        echo 'group = www-data' | tee --append ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf > /dev/null
        ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
    fi
}

function install_wp {
    log 'Installing WordPress'
    local WP_LINK=https://wordpress.org/wordpress-$WP_VERSION.tar.gz
    local WP_PATCH=../wordpress-$WP_VERSION.patch

    cd /tmp/wpti
    rm --recursive --force wordpress
    log '.. downloading'
    cd /tmp/wpti/cache
    wget --no-clobber $WP_LINK
    tar --extract --ungzip --file=wordpress-$WP_VERSION.tar.gz --directory=..
    cd ../wordpress
    log '.. clean up plugins'
    rm --recursive --force wp-content/plugins
    mkdir --parents wp-content/plugins
    if [ -f $WP_PATCH ];
    then
        # To create patch use: diff --unified path/to/original.php path/to/fix.php > $WP_PATCH
        log '.. patching wordpress'
        patch -p0 < $WP_PATCH
    fi
    cp ../wp-config.php wp-config.php
    log '.. installing'
    wget --quiet --output-document=- --post-data='weblog_title=wpti&user_name=wpti&admin_password=wpti&admin_password2=wpti&admin_email=wpti%40wpti.dev&blog_public=1' 'http://wpti.dev:8000/wp-admin/install.php?step=2' | grep installed
}

function set_db_engine {
    log "Setting DB engine to $DB_ENGINE"
    mysql --user=root wpti --execute 'SHOW TABLES' | tail -n+2 | xargs -i mysql wpti --execute "ALTER TABLE {} ENGINE=$DB_ENGINE"
}

function install_plugin {
    log 'Installing plugin'
    PLUGIN=/tmp/wpti/wordpress/wp-content/plugins/wp-testing
    rm --recursive --force $PLUGIN
    mkdir --parents $PLUGIN
    cd $HERE/../..
    git checkout-index --all --force --prefix=$PLUGIN/
    cd $PLUGIN
    ln --symbolic composer.lock.dist composer.lock
    if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
    then
        phpenv shell 5.3
        composer install --no-dev --no-ansi --no-interaction --no-progress --optimize-autoloader --prefer-dist
        phpenv shell --unset
    else
        composer install --no-dev --no-ansi --no-interaction --no-progress --optimize-autoloader --prefer-dist
    fi
    cd $HERE
}

function install_upgrade_plugin {
    [[ "$WP_UPGRADE" == "0" ]] && return 0

    log 'Installing plugin for upgrade test'
    cd /tmp/wpti
    mkdir --parents wordpress/wp-content/plugins/hello-dolly
    cp hello.php wordpress/wp-content/plugins/hello-dolly
}

function install_other_plugins {
    [[ "$PLUGINS" == "" ]] && return 0

    log 'Installing other plugins'
    cd /tmp/wpti/cache
    items=(${PLUGINS//:/ })
    for i in "${!items[@]}"
    do
        PLUGIN_NAME="${items[i]}"
        PLUGIN_URL="https://downloads.wordpress.org/plugin/$PLUGIN_NAME.zip"
        log ".. $PLUGIN_NAME"

        wget --no-clobber $PLUGIN_URL
        unzip -oq $PLUGIN_NAME.zip -d /tmp/wpti/wordpress/wp-content/plugins/
    done
}

function log {
    local message=$1
    local now=$(date)
    echo
    echo [$now] $message
}

function realpath {
    local path=$1
    php -r 'echo realpath($argv[1]);' $path
}

function main {
    init
    db
    setup_link
    start_nginx
    php_cgi
    install_wp
    set_db_engine
    install_other_plugins
    install_plugin
    install_upgrade_plugin
    log 'Done.'
}

main
