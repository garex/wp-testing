#!/usr/bin/env bash

TO=$1
SUBJECT="$2"
ATTACH="$3"

echo "$SUBJECT" | mutt -a "$ATTACH" -s "$SUBJECT" -- $TO
