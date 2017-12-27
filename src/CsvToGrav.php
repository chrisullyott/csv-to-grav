<?php

/**
 * Convert a CSV of posts into Grav pages.
 *
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class CsvToGrav
{
    const OUTPUT_DIR = 'grav';

    private $posts = array();

    public function __construct($file, array $columnMap = array())
    {
        $this->setFile($file);
        $this->setColumnMap($columnMap);
    }

    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    public function setColumnMap(array $columnMap)
    {
        $this->columnMap = $columnMap;

        return $this;
    }

    private function getRows()
    {
        $parser = new CsvParser($this->file);

        return $parser->getItems();
    }

    private function getPosts()
    {
        $posts = array();

        foreach ($this->getRows() as $row) {
            $data = array();

            foreach ($this->columnMap as $gravKey => $columnName) {
                $data[$gravKey] = $row[$columnName];
            }

            $posts[] = new Post($data);
        }

        return $posts;
    }

    public function build()
    {
        $created = 0;

        foreach ($this->getPosts() as $post) {
            $path = File::path(
                self::OUTPUT_DIR,
                $post->year,
                $post->slug,
                'item.md'
            );

            File::createDir(dirname($path));

            if (file_put_contents($path, $post->getGravFile())) {
                $created++;
            }
        }

        return $created;
    }
}
