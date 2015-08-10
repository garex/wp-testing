#!/usr/bin/env php
<?php
$file = file('php://stdin');
array_shift($file);

$tags = [
    'feature' => [],
    'bug'     => [],
    'support' => [],
];

// Create tags
foreach ($file as $line) {
    list($category, $tag) = explode("\t", $line);
    !isset($tags[$category][$tag]) && $tags[$category][$tag] = 0;
    $tags[$category][$tag]++;
}

// Mark popular
foreach ($tags as $category => &$categoryTags) {
    arsort($categoryTags);
    $popularIndex = round(count($categoryTags) * 0.2);
    $counter = 0;
    foreach ($categoryTags as $tag => $topicsCount) {
        $categoryTags[$tag] = ($counter < $popularIndex);
        $counter++;
    }
}

// Last sort by name
foreach ($tags as $category => &$categoryTags) {
    ksort($categoryTags);
}

// Output
foreach ($tags as $category => &$categoryTags) {
    echo md_popular(ucfirst($category)) . ': ';
    echo implode(', ', array_map(function($tag, $isPopular) {
        return $isPopular ? md_popular_tag_link($tag) : md_tag_link($tag);
    }, array_keys($categoryTags), $categoryTags));
    echo '.'. PHP_EOL . PHP_EOL;
}

function md_tag_link($tag) {
    $url   = 'https://wordpress.org/tags/wp-testing-' . strtolower($tag);
    $label = str_replace('-', ' ', $tag);
    return sprintf('[%s](%s)', $label, $url);
}

function md_popular($text) {
    return sprintf('**%s**', $text);
}


function md_popular_tag_link($tag) {
    return md_popular(md_tag_link($tag));
}
