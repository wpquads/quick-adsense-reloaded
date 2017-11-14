<?php

namespace wpquads;

class adsTxt {

    /**
     * Content to add
     * @var string 
     */
    private $content;

    /**
     * Pattern to search and replace
     * @var string 
     */
    private $pattern;

    /**
     * 
     * @param array $content to add
     * @param string $pattern for content to remove
     */
    public function __construct($content = array(), $pattern = '') {
        $this->content = $content;

        $this->pattern = $pattern;

        $this->filename = ABSPATH . 'ads.txt';
    }

    /**
     * Write ads.txt
     * @return bool
     */
    public function writeAdsTxt() {
        if (false !== file_put_contents($this->filename, $this->getContent())) {
            return true;
        }
        return false;
    }

    /**
     * Create and return the content
     * @return string
     */
    public function getContent() {
        // ads.txt does not exists
        if (!is_file($this->filename)) {
            return $this->content . "\r\n";
        }

        // get everything from ads.txt and convert to array
        $contentText = file_get_contents($this->filename);

        $content = array_filter(explode("\n", trim($contentText)), 'trim');
        
        // Pattern not find so append new content to ads.txt existing content  
        if (strpos($contentText, $this->pattern) === false) {
            return $contentText . "\r\n" . $this->content;
        }

        // Pattern found, so remove everything first and add new stuff from api response
        $newContent = '';
        foreach ($content as $entry) {
            if (strpos($entry, $this->pattern) !== false) {
               continue; 
            }
                $newContent .= str_replace(array("\r", "\n", " "), '', $entry) . "\r\n";
            
        }
        return $newContent . $this->content;
    }

}
