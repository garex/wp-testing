#!/usr/bin/env php
<?php

namespace Wpt;

use mysqli;

define('ROOT', realpath(__DIR__.'/../../../'));

$DB_ENGINE = getenv('DB_ENGINE') ?: 'InnoDB';
$DB_CHARSET = getenv('DB_CHARSET') ?: 'utf8';
$WP_VERSION = getenv('WP_VERSION') ?: '5.4';
$WP_T_MULTI_SERVER = getenv('WP_T_MULTI_SERVER') ?: 'http://wpt.docker';
$PLUGIN = ROOT.'/wordpress/wp-content/plugins/wp-testing';

log('Installing vendors');
symlink('composer.lock.dist', 'composer.lock');
echo shell_exec('composer install --ansi --no-interaction --no-progress --optimize-autoloader --prefer-dist');

echo shell_exec('cd db && ../vendor/bin/ruckus.php db:migrate');

log('Creating DB and user');
$mysqli = new mysqli();
$tries = 0;
while ($tries++ < 20) {
    if (@!$mysqli->real_connect('db', 'root', '123456')) {
        log('Connect error: '.$mysqli->connect_error);
        sleep(1);
    }
}

if ($mysqli->connect_error) {
    log('Still can not connect...');
    exit(2);
}

$mysqli->query('DROP DATABASE IF EXISTS wpti');
$mysqli->query("CREATE DATABASE wpti DEFAULT CHARACTER SET '$DB_CHARSET'");
$mysqli->select_db('wpti');
$mysqli->query('GRANT USAGE ON wpti.* TO wpti');
$mysqli->query('DROP USER wpti');
$mysqli->query('CREATE USER wpti IDENTIFIED BY "wpti"');
$mysqli->query('GRANT ALL ON wpti.* TO wpti');

log('Installing WordPress');
$WP_LINK="https://wordpress.org/wordpress-$WP_VERSION.tar.gz";
$WP_FILE="cache/wordpress-$WP_VERSION.tar.gz";

if (file_exists($PLUGIN) && is_link($PLUGIN)) {
    unlink($PLUGIN);
}
echo shell_exec('rm -rf wordpress');

log('.. downloading');

echo shell_exec("curl -s -z $WP_FILE -o $WP_FILE $WP_LINK");
echo shell_exec("tar -xzf $WP_FILE");

$config = file_get_contents(ROOT.'/tests/integration-environment/wp-config.php');
$config = str_replace('utf8', $DB_CHARSET, $config);
file_put_contents(ROOT.'/wordpress/wp-config.php', $config);

log('.. installing');
echo shell_exec("wget -q -O - --post-data='weblog_title=wpti&user_name=wpti&admin_password=wpti&admin_password2=wpti&admin_email=wpti%40wpti.dev&blog_public=1' '$WP_T_MULTI_SERVER/wp-admin/install.php?step=2' | grep installed");

log("Setting DB engine to $DB_ENGINE");
$mysqli->query('SET GLOBAL sql_mode = "ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"');
if (!($result = $mysqli->query('select table_name from information_schema.TABLES  where table_schema = "wpti"'))) {
    log('Can not fetch tables');
    exit(2);
}
while($row = $result->fetch_object()){
    $row->table_name;
    $mysqli->query("ALTER TABLE $row->table_name ENGINE=$DB_ENGINE");
}

log('Installing plugin');

echo shell_exec('rm -rf '.$PLUGIN);
echo shell_exec('mkdir --parents '.$PLUGIN);
echo shell_exec('git checkout-index --all --force --prefix='.$PLUGIN.'/');
echo shell_exec('cd '.$PLUGIN.' && ln -s composer.lock.dist composer.lock && composer install --no-dev --no-ansi --no-interaction --no-progress --optimize-autoloader --prefer-dist');

log('Remove other plugins');
echo shell_exec('rm -rf wordpress/wp-content/plugins/akismet wordpress/wp-content/plugins/hello.php');

function log($message) {
    $now = date(DATE_ATOM);
    echo "[$now] $message\n";
}
