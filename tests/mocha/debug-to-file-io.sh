#!/usr/bin/env bash

ATTACHMENTS="$1"

for ATTACHMENT in $ATTACHMENTS; do
    curl --form "file=@"$ATTACHMENT https://file.io/?expires=1d;
    echo;
done;
