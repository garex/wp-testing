#!/usr/bin/env bash

TO=$1
SUBJECT="$2"
ATTACH="$3"

ls $ATTACH 2>/dev/null || exit 0

which mutt || sudo apt-get install mutt

echo "$SUBJECT" | mutt -s "$SUBJECT" $(printf -- '-a %q ' $ATTACH) -- $TO
