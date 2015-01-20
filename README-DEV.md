# Wp-testing for developer

## To regenerate README.md

Changes should be in readme.txt, which then generates README.md by this:

    vendor/bin/wp2md convert < readme.txt > README.md

* Validator is at http://wordpress.org/plugins/about/validator/
* Template is at https://wordpress.org/plugins/about/readme.txt
* Good howto is at http://www.smashingmagazine.com/2011/11/23/improve-wordpress-plugins-readme-txt/

## To install composer package

* Copy-paste "samyk/evercookie" and "uuid-js" packages into your "repositories"
