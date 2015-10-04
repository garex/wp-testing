
set -e

HERE=`pwd`
cd ../../db

mysql -e 'drop database if exists wp_testing_4_3_1'
mysql -e 'create database wp_testing_4_3_1 DEFAULT CHARACTER SET utf8'
../vendor/bin/ruckus.php db:migrate

cd $HERE
mysqldump wp_testing_4_3_1 |\
sed 's/[0-9]\{4\}-[0-9]\{2\}-[0-9]\{2\} *[0-9]\{1,2\}:[0-9]\{2\}:[0-9]\{2\}/xxxx-xx-xx xx:xx:xx/g' |\
sed 's/[a-f0-9][a-f0-9-]\{34\}[a-f0-9]/xxxxxxxxxxxxxxxx/' > current.sql

diff master.sql current.sql
