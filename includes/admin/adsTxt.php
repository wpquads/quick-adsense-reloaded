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
     * Check if we need to create an ads.txt
     * @return boolean
     */
    public function needsAdsTxt(){
        if (!is_file($this->filename)){
            return true;
        }
        
        // get everything from ads.txt and convert to array
        $contentText = quads_local_file_get_contents($this->filename);
        
        // Pattern not find 
        if (strpos($contentText, $this->pattern) === false) {
            return true;
        } else {
            return false;
        }
        
    }

    /**
     * Write ads.txt
     * @return bool
     */
    public function writeAdsTxt() {
        if (false !== file_put_contents($this->filename, $this->getContent())) {
            // show notice that ads.txt has been created
            set_transient('quads_vi_ads_txt_notice', true, 300);
            return true;
        }
        // show error admin notice
        set_transient('quads_vi_ads_txt_error', true, 300);
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
        $contentText = quads_local_file_get_contents($this->filename);
        
        // Change all \r\n to \n
        //$contentText = str_replace(array("\r\n", "\n"), '', $contentText);

        //$content = array_filter(explode("\n", trim($contentText)), 'trim');
        $content = explode("\n", $contentText);
        
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
                $newContent .= str_replace(array("\r", "\n"), '', $entry) . "\r\n";
            
        }
        return $newContent . $this->content;
    }

}