#!/usr/bin/env bash

SOURCE=tools/deploy/plugin-update-checker-usage-notice.php
TARGET=vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php

grep --quiet wp-testing $TARGET && exit 0

MEDIATOR=$(mktemp)

cp $TARGET $MEDIATOR
cp $SOURCE $TARGET
tail -n +2 $MEDIATOR >> $TARGET
