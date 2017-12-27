<?php

/**
 * @author Chris Ullyott <contact@chrisullyott.com>
 */
class Post
{
    private static $dateFormat = 'H:i m/d/Y';

    private static $requiredFields = array(
        'title',
        'date',
        'html'
    );

    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        $this->preflight()->provision();
    }

    private function preflight()
    {
        foreach (self::$requiredFields as $field) {
            if (!property_exists($this, $field)) {
                throw new Exception("Required field {$field} undefined");
            }
        }

        return $this;
    }

    private function provision()
    {
        $this->slug = slugifier\slugify($this->title);
        $this->date = date(self::$dateFormat, strtotime($this->date));
        $this->year = date('Y', strtotime($this->date));

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
