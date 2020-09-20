#!/usr/bin/env sh

cd vendor
rm -rf ruckusing/ruckusing-migrations/tests
rm -rf nikic/php-parser/grammar
rm -rf nikic/php-parser/test
rm -rf nikic/php-parser/test_old
rm -rf samyk/evercookie/assets/evercookie_sl
rm -f  samyk/evercookie/assets/evercookie.jar
rm -f  samyk/evercookie/assets/evercookie.jnlp
rm -f  samyk/evercookie/assets/*.java
rm -rf broofa/node-uuid/benchmark
rm -rf broofa/node-uuid/bin
rm -rf broofa/node-uuid/test
rm -rf $(find -iname 'test?' -type d)
