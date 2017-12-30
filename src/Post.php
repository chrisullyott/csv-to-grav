<?php

/**
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class Post
{
    private static $dateFormat = 'm/d/Y g:ia';

    public function __construct(array $data, array $metaData, $isPublished = false)
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

        $converter = new League\HTMLToMarkdown\HtmlConverter();
        $this->markdown = $converter->convert($this->html);

        return $this;
    }

    private function getHeaders()
    {
        $data = array(
            'published' => $this->isPublished,
            'title'     => $this->title,
            'slug'      => $this->slug,
            'date'      => $this->date,
            'author'    => $this->author,
            'taxonomy' => array(
                'category' => $this->category,
                'tag'      => $this->tag
            ),
            'metadata' => array_merge($this->metaData, array(
                'og:title' => $this->title
            ))
        );

        return Symfony\Component\Yaml\Yaml::dump($data);
    }

    public function getGravFile()
    {
        $file  = "---\n";
        $file .= "# https://learn.getgrav.org/content\n\n";
        $file .= trim($this->getHeaders()) . "\n";
        $file .= "---\n\n";
        $file .= "{$this->markdown}";

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
