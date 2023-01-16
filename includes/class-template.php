<?php

namespace wpquads;

/*
 * Class for rendering templates
 */

class template {
    protected $path, $data;

    /**
     * 
     * @param string $path
     * @param array $data
     */
    public function __construct($path, $data = array()) {
        $this->path = QUADS_PLUGIN_DIR . DIRECTORY_SEPARATOR . $path . '.php';
        $this->data = $data;
    }

    /**
     * 
     * @return string HTML
     */
    public function render() {
        if(file_exists($this->path)){
            //Extracts vars to current view scope
            extract($this->data);

            //Starts output buffering
            ob_start();

            //Includes contents
            include $this->path;
            $buffer = ob_get_contents();
            @ob_end_clean();

            //Returns output buffer
            return $buffer;
        } else {
            //Throws exception
        }
    }       
}