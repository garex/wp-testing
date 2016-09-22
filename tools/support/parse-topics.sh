#!/usr/bin/env bash

# Exit immediately if a command exits with a non-zero status.
set -e

function parse_topics {
    local topics=$1
    local where=$2
    for topic in $where; do
      parse_topic $topics $topic
    done
}

function parse_tags_in_file {
    local file=$1
    egrep --no-filename --only-matching 'https://wordpress.org/support/topic-tag/wp-testing-[a-z0-9-]+' $file | sed 's/https:\/\/wordpress.org\/support\/topic-tag\/wp-testing-//' | sed 's/\(feature\|bug\|support\)/000-\1/g' | sort | sed 's/000-//g' | uniq
}

function parse_url {
    local file=$1
    grep -m1 -o 'Mirrored from wordpress.org/support/topic/[^ ]* ' $file | sed 's/Mirrored from //'
}

function parse_title {
    local file=$1
    egrep --max-count=1 --no-filename --only-matching '<title>.+</title>' $file | sed 's/<title>Topic: //' | sed 's/ &laquo;  WordPress.org Forums<\/title>//' | sed 's/\[Resolved\] //'
}

function parse_topic {
    local topics=$1
    local file=$2
    local tags=$(parse_tags_in_file $file | paste -d',' -s | sed 's/,/, /g')
    local url=$(parse_url $file)
    local title=$(parse_title $file)
    local status=$(grep -o '\[Resolved\]' $file > /dev/null && echo resolved || echo new)
    local starter=$(grep -o 'Started [0-9 a-z]* ago by [^<]*' $file | sed 's/Started [0-9 a-z]* ago by //')
    local abstract=$(egrep -zo --max-count=1 '<div class="bbp-topic-content">\s+<p>[^<]*?</p>' $file | tr '\n' ' ' | sed 's/<[^>]*>//g' | sed 's/^\s*\(.*\)\s*$/\1/')

    local status_in_tags=$(extract_status_from_tags "$tags")
    if [ ! -z $status_in_tags ]; then
      local tags=$(remove_status_from_tags "$tags")
      local status=$status_in_tags
    fi

    echo -e -n "$status\t$title\t$starter\t$tags\t$url\t" | tee --append $topics
    echo "$abstract" | tee --append $topics
}

function prepare_topics_header {
    local topics=$1
    echo -e "status\ttitle\tstarter\ttags\turl\tabstract" | tee $topics
}

function prepare_tags_header {
    local tags_file=$1
    echo -e "category\ttag\turl\ttitle" | tee $tags_file
}

function parse_tags {
    local tags_file=$1
    local where=$2

    for topic in $where; do
      parse_tag $topic | tee --append $tags_file
    done
}

function parse_tag {
    local file=$1

    local tags=$(parse_tags_in_file $file | egrep -v '(not-confirmed|not-related|auto-closed|duplicate|rejected)')
    local url=$(parse_url $file)
    local title=$(parse_title $file)

    local category=other
    $(echo $tags | grep -q support) && local category=support
    $(echo $tags | grep -q bug) && local category=bug
    $(echo $tags | grep -q feature) && local category=feature

    for tag in $(echo "$tags" | egrep -v '(other|support|bug|feature)'); do
      echo -e "$category\t$tag\t$url\t$title"
    done
}

function extract_status_from_tags {
    local tags=$1
    echo "$tags," | egrep -o --max-count=1 '(not-confirmed|not-related|auto-closed|duplicate|rejected)-?' | grep -v '\-$'
}

function remove_status_from_tags {
    local tags=$1
    local status_in_tags=$(extract_status_from_tags "$tags")

    if [ ! -z $status_in_tags ]
    then
      echo $tags | sed -e "s/$status_in_tags[, ]*//" -e "s/[, ]*$status_in_tags//"
    else
      echo $tags
    fi
}

function main {
    prepare_topics_header topics.txt
    parse_topics topics.txt './wordpress-support-mirror/wordpress.org/support/topic/*/*.html'
    prepare_tags_header tags.txt
    parse_tags tags.txt './wordpress-support-mirror/wordpress.org/support/topic/*/*.html'

    cat tags.txt | ./generate-markdown-tags.php > TAGS.md
}

main
