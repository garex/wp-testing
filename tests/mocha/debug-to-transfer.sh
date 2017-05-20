#!/usr/bin/env bash

ATTACHMENTS="$1"

for ATTACHMENT in $ATTACHMENTS; do
    curl --header "Max-Days: 1" --upload-file $ATTACHMENT https://transfer.sh/$(basename $ATTACHMENT);
    echo;
done;
