#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

HERE=$(dirname $(realpath $0))
DB_ENGINE=MyISAM
WP_VERSION=4.0


function db {
    log 'Creating DB and user'
    mysql --execute '
        DROP DATABASE IF EXISTS wpti;
        CREATE DATABASE wpti;

        GRANT USAGE ON wpti.* TO wpti;
        DROP USER wpti;

        CREATE USER wpti IDENTIFIED BY "wpti";
        GRANT ALL ON wpti.* TO wpti;
    '
}

function dns {
    log 'Setting up DNS'
    grep wpti.dev /etc/hosts > /dev/null || echo '127.0.0.1   wpti.dev' >> /etc/hosts
}

function setup_link {
    log 'Setting up symbolic link'
    rm --force /tmp/wpti
    ln --symbolic $HERE /tmp/wpti
}

function nginx {
    log 'Configuring and reloading nginx'
    rm --force /etc/nginx/sites-enabled/wpti
    ln --symbolic /tmp/wpti/wpti.nginx.conf /etc/nginx/sites-enabled/wpti
    sudo service nginx reload
}

function install_wp {
    log 'Installing WordPress'
    local WP_LINK=https://wordpress.org/wordpress-$WP_VERSION.tar.gz

    cd /tmp/wpti
    sudo rm --recursive --force wordpress
    log '.. downloading'
    wget --no-clobber $WP_LINK
    tar --extract --ungzip --file=wordpress-$WP_VERSION.tar.gz
    cd wordpress
    log '.. clean up plugins'
    rm --recursive --force wp-content/plugins
    mkdir --parents wp-content/plugins
    cp ../wp-config.php wp-config.php
    log '.. installing'
    wget --output-document=- --post-data='weblog_title=wpti&user_name=wpti&admin_password=wpti&admin_password2=wpti&admin_email=wpti%40wpti.dev&blog_public=1' 'http://wpti.dev/wp-admin/install.php?step=2' > /dev/null
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
    composer install --no-interaction --no-dev --prefer-dist
    sudo chown -R www-data:www-data .
    cd $HERE
}

function log {
    local message=$1
    local now=$(date)
    echo
    echo [$now] $message
}

function main {
    db
    dns
    setup_link
    nginx
    install_wp
    set_db_engine
    install_plugin
    log 'Done.'
}

main
