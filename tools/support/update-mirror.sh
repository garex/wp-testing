#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

mkdir -p wordpress-support-mirror
cd wordpress-support-mirror
httrack --ext-depth=1 --update --updatehack --cache=2 --display -'*' +'*/support/topic/*' +'*/wp-testing/page/*' https://wordpress.org/support/plugin/wp-testing
