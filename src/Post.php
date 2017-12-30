<?php

/**
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class Post
{
    private static $dateFormat = 'm/d/Y g:ia';

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $this->provision();
    }

    private function provision()
    {
        $this->slug = slugifier\slugify($this->title);
        $this->date = date(self::$dateFormat, strtotime($this->date));

        $converter = new League\HTMLToMarkdown\HtmlConverter();
        $this->markdown = $converter->convert($this->html);

        return $this;
    }

    public function getGravFile()
    {
        $file  = "---\n";
        $file .= "title: {$this->title}\n";
        $file .= "date: {$this->date}\n";
        $file .= "author: {$this->author}\n";
        $file .= "---\n\n";
        $file .= "{$this->markdown}";

        return $file;
    }
}
