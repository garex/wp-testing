#!/usr/bin/env bash

# tnx to https://gist.github.com/fxbenard/9367794

# Pull all files from Transifex;
tx pull --all --force

# Create .mo files from .po files.
# Twisted by WP-Translations.org, created by grappler.

for file in $(find . -name "*.po")
do
    echo Compile $file
    msgfmt -o ${file/.po/.mo} $file
done

echo Adding to git
git add *.po
git add *.mo
