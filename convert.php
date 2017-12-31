<?php

date_default_timezone_set('UTC');

include 'vendor/autoload.php';

$conversion = new CsvToGrav('example.csv');

// Set a directory for all posts.
$conversion->setOutputDir('user/pages/01.blog');

// Set the column map (Grav -> CSV).
$conversion->setColumnMap(array(
    'title'    => 'title_field',    // string
    'date'     => 'date_field',     // string
    'html'     => 'html_field',     // string
    'author'   => 'author_field',   // string
    'category' => 'category_field', // comma-separated list
    'tag'      => 'tag_field'       // comma-separated list
));

// Set metadata which will be the same for all posts.
$conversion->setMetaData(array(
    'generator' => 'Grav',
    'og:locale' => 'en_US',
    'og:type'   => 'article'
));

// Set whether posts are published default.
$conversion->setIsPublished(false);

// Generate.
$count = $conversion->build();

echo "Created {$count} items.\n";
