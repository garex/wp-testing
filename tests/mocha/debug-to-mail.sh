#!/usr/bin/env bash

TO=$1
SUBJECT="$2"
ATTACH="$3"

ls $ATTACH 2>/dev/null || exit 0

echo "$SUBJECT" | mailx -s "$SUBJECT" $(printf -- '-a %q ' $ATTACH) -- $TO
