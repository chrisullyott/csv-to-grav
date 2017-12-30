<?php

date_default_timezone_set('UTC');

include 'vendor/autoload.php';

$conversion = new CsvToGrav('example.csv');

$conversion->setColumnMap(array(
    'title'  => 'example_title_field',
    'date'   => 'example_date_field',
    'html'   => 'example_html_field',
    'author' => 'example_author_field'

$conversion->setMetaData(array(
    'generator'    => 'Grav',
    'og:locale'    => 'en_US',
    'og:type'      => 'article'
));

$count = $conversion->build();

echo "Created {$count} items.\n";
