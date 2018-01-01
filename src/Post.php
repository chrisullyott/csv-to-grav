<?php

/**
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class Post
{
    private static $dateFormat = 'm/d/Y g:ia';

    public function __construct(array $data, array $metaData = array(), $isPublished = false)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $this->metaData = $metaData;
        $this->isPublished = $isPublished;

        $this->provision();
    }

    private function provision()
    {
        $this->slug = slugifier\slugify($this->title);
        $this->date = date(self::$dateFormat, strtotime($this->date));

        $categories = self::explodeList($this->category);
        $this->category = strtolower($categories[0]);

        $tags = self::explodeList($this->tag);
        $this->tag = array_map('strtolower', $tags);

        return $this;
    }

    private function getAllMetaData()
    {
        $toMerge = array(
            'generator' => 'Grav',
            'og:title' => $this->title
        );

        return array_merge($toMerge, $this->metaData);
    }

    private function getHeaders()
    {
        $data = array(
            'published' => $this->isPublished,
            'title'     => $this->title,
            'slug'      => $this->slug,
            'date'      => $this->date,
            'taxonomy' => array(
                'author'   => $this->author,
                'category' => $this->category,
                'tag'      => $this->tag
            ),
            'metadata' => $this->getAllMetaData()
        );

        return trim(Symfony\Component\Yaml\Yaml::dump($data)) . "\n";
    }

    private function getBody()
    {
        $converter = new League\HTMLToMarkdown\HtmlConverter();

        return trim($converter->convert($this->html)) . "\n";
    }

    public function getGravFile()
    {
        $file  = "---\n";
        $file .= "# https://learn.getgrav.org/content\n\n";
        $file .= $this->getHeaders();
        $file .= "---\n\n";
        $file .= $this->getBody();

        return $file;
    }

    private static function explodeList($list, $delimiter = ',')
    {
        $items = explode($delimiter, $list);
        $items = array_map('trim', $items);
        $items = array_filter($items);

        return array_values($items);
    }
}
