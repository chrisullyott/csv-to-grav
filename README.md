# csv-to-grav

[Grav](https://getgrav.org/) uses [Markdown](https://daringfireball.net/projects/markdown/syntax) files instead of a database to serve content. When migrating your website or blog to Grav, you'll need to convert your existing HTML content to Markdown, and arrange your posts in a directory structure that Grav can understand.

Some specific [migration assistants](https://learn.getgrav.org/migration) are available to help you do this, but if you just want to convert an arbitrary CSV into Grav posts, this tool can help. In summary, **csv-to-grav** reads a CSV file and builds a Grav file tree, converting your content to Markdown and your metadata to Yaml.

## Setup

Install dependencies with [Composer](https://getcomposer.org/).

## Run

Modify convert.php ...

```
$conversion = new CsvToGrav('example.csv');

$conversion->setColumnMap(array(
    'title' => 'example_title_field',
    'date'  => 'example_date_field',
    'html'  => 'example_html_field'
));

$count = $conversion->build();

echo "Created {$count} items.\n";

```

First, replace `example.csv` with the path of your spreadsheet. Then, fill in the `example_` field map with the names of the appropriate spreadsheet column. To create a Grav page, it's required to have a title, a date, and HTML content.

Finally, run the script! A directory `grav` will be created with the posts ready to go.

```
$ php convert.php
```
