#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

HERE=$(dirname $0)
DB_ENGINE=${DB_ENGINE:-InnoDB}
WP_VERSION=${WP_VERSION:-4.1.1}
PLUGINS=${PLUGINS:-}

function init {
    log 'Define vars'
    HERE=$(dirname $(realpath $0))
}


function db {
    log 'Creating DB and user'
    sudo mysql --execute '
        DROP DATABASE IF EXISTS wpti;
        CREATE DATABASE wpti DEFAULT CHARACTER SET utf8;

        GRANT USAGE ON wpti.* TO wpti;
        DROP USER wpti;

        CREATE USER wpti IDENTIFIED BY "wpti";
        GRANT ALL ON wpti.* TO wpti;
    '
}


function setup_link {
    log 'Setting up symbolic link'
    sudo rm --force /tmp/wpti
    ln --symbolic $HERE /tmp/wpti
    sudo chown --no-dereference :www-data /tmp/wpti
}

function nginx {
    log 'Configuring and reloading nginx'
    sudo apt-get install nginx
    sudo rm --force /etc/nginx/sites-enabled/wpti
    sudo ln --symbolic /tmp/wpti/wpti.nginx.conf /etc/nginx/sites-enabled/wpti
    sudo service nginx restart
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
        USER=www-data PHP_FCGI_CHILDREN=15 PHP_FCGI_MAX_REQUESTS=1000 sudo $PHP_CGI --bindpath 127.0.0.1:9000 &
    else
        sudo cp ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf.default ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf
        echo 'user  = www-data' | sudo tee --append ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf > /dev/null
        echo 'group = www-data' | sudo tee --append ~/.phpenv/versions/$(phpenv version-name)/etc/php-fpm.conf > /dev/null
        sudo ~/.phpenv/versions/$(phpenv version-name)/sbin/php-fpm
    fi
}

function install_wp {
    log 'Installing WordPress'
    local WP_LINK=https://wordpress.org/wordpress-$WP_VERSION.tar.gz
    local WP_PATCH=../wordpress-$WP_VERSION.patch

    cd /tmp/wpti
    sudo rm --recursive --force wordpress
    log '.. downloading'
    wget --no-clobber $WP_LINK
    tar --extract --ungzip --file=wordpress-$WP_VERSION.tar.gz
    cd wordpress
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
    wget --quiet --output-document=- --post-data='weblog_title=wpti&user_name=wpti&admin_password=wpti&admin_password2=wpti&admin_email=wpti%40wpti.dev&blog_public=1' 'http://wpti.dev/wp-admin/install.php?step=2'
}

function set_db_engine {
    log "Setting DB engine to $DB_ENGINE"
    mysql wpti --execute 'SHOW TABLES' | tail -n+2 | xargs -i mysql wpti --execute "ALTER TABLE {} ENGINE=$DB_ENGINE"
}

function install_plugin {
    log 'Installing plugin'
    PLUGIN=/tmp/wpti/wordpress/wp-content/plugins/wp-testing
    rm --recursive --force $PLUGIN
    mkdir --parents $PLUGIN
    cd $HERE/../..
    git checkout-index --all --force --prefix=$PLUGIN/
    cd $PLUGIN
    if [[ "$TRAVIS_PHP_VERSION" == "5.2" ]];
    then
        phpenv shell 5.3
        composer install --no-interaction --no-dev --prefer-dist
        phpenv shell --unset
    else
        composer install --no-interaction --no-dev --prefer-dist
    fi
    sudo chown -R www-data:www-data .
    cd $HERE
}

function install_other_plugins {
    [[ "$PLUGINS" == "" ]] && return 0

    log 'Installing other plugins'
    cd /tmp/wpti
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
    nginx
    php_cgi
    install_wp
    set_db_engine
    install_other_plugins
    install_plugin
    log 'Done.'
}

main
