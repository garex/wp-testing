cd wordpress-support-mirror/wordpress.org/support/topic

for file in **/index.html; do
    echo '# '$file
    cat $file | tidy -q -asxml --show-warnings no --wrap 0 | xpath -e '//p[@class="bbp-user-nicename"][text()="(@ustimenko)"]/../../*[@class="bbp-reply-content"]/p' 2>/dev/null | sed 's/<[^>]\+>//g'
    echo
    echo
done;
