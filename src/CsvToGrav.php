<?php

/**
 * Convert a CSV of posts into Grav pages.
 *
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class CsvToGrav
{
    private static $requiredFields = array(
        'title',
        'date',
        'html'
    );

    private $rows = array();
    private $posts = array();
    private $metaData = array();
    private $isPublished = false;

    public function __construct($file, array $columnMap = array())
    {
        $this->setFile($file);
        $this->setColumnMap($columnMap);
        $this->setOutputDir('grav');
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

    public function setMetaData(array $metaData)
    {
        $this->metaData = $metaData;

        return $this;
    }

    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function setOutputDir($outputDir)
    {
        $this->outputDir = $outputDir;

        return $this;
    }

    private function getRows()
    {
        if (!$this->rows) {
            $parser = new CsvParser($this->file);
            $this->rows = $parser->getItems();
        }

        return $this->rows;
    }

    private function getColumnNames()
    {
        $rows = $this->getRows();

        return array_keys($rows[0]);
    }

    private function getPosts()
    {
        if (!$this->posts) {
            foreach ($this->getRows() as $row) {
                $data = array();

                foreach ($this->columnMap as $gravKey => $columnName) {
                    $data[$gravKey] = $row[$columnName];
                }

                $this->posts[] = new Post($data, $this->metaData, $this->isPublished);
            }
        }

        return $this->posts;
    }

    private function preflight()
    {
        // Whether all required fields exist in the column map.
        foreach (self::$requiredFields as $field) {
            if (!in_array($field, array_keys($this->columnMap))) {
                throw new Exception("Required field \"{$field}\" undefined");
            }
        }

        // Whether all column map fields exist in the spreadsheet.
        foreach ($this->columnMap as $field) {
            if (!in_array($field, $this->getColumnNames())) {
                throw new Exception("Referenced field \"{$field}\" not found");
            }
        }

        return $this;
    }

    private function uniqueDirPath($path)
    {
        $i = 0;
        $s = '';

        while (is_dir("{$path}{$s}")) {
            $s = '-' . ++$i;
        }

        return "{$path}{$s}";
    }

    public function build()
    {
        $this->preflight();

        $created = 0;

        foreach ($this->getPosts() as $post) {
            $filePath = File::path($this->outputDir, $post->slug, 'item.md');
            $dirPath = self::uniqueDirPath(dirname($filePath));
            File::createDir($dirPath);

            if (file_put_contents($filePath, $post->getGravFile())) {
                $created++;
            }
        }

        return $created;
    }
}
