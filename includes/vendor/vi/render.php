<?php

namespace wpquads\render;

//use wpquads;

/*
 * vi ad render for WP QUADS used on front page
 * 
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

class render {

    /**
     * All Ad Settings
     * @var array 
     */
    private $ads;
    
    /**
     * Curent ad id
     * @var int
     */
    private $id;

    /**
     * Filtered Content
     * @var string 
     */
    private $content;

    public function __construct() {
        $this->ads = get_option('quads_vi_ads');
        add_filter('the_content', array($this, 'prepareOutput'));
    }

    public function prepareOutput($content) {

        if (empty($this->ads['ads'])) {
            return $content;
        }

        If (empty($this->content)) {
            $this->content = $content;
        }

        // Loop through all available ads and use the return value as new $content
        foreach ($this->ads['ads'] as $key => $value) {
            //echo 'testerdfdf' . $key;
            $this->id = $key;
            $this->content = $this->filterContent();
        }
        
        return $this->content;
    }

    /**
     * Filter the content for available ad positions
     * and prepare for embeding specific ad
     * @param string $content
     * @param int $id
     */
    public function filterContent() {

        // Loop through all available filter methods and run them until one of the filters returns sucessfully
        $methods = get_class_methods(get_class());
        $loop = true;
        foreach ($methods as $method) {

            if ($loop == false) {
                break;
            }
            // Do not use method filterContent()
            if (strpos($method, 'filter') !== false && $method != 'filterContent') {
                // Set content to filtered content
                if (true == $this->$method()) {
                    $loop = false;
                }
            }
        }
        return do_shortcode($this->content);
    }

    private function filterAbovePost() {
        if (!isset($this->ads['ads'][$this->id]['abovePost'])) {
            $this->content = $this->render($this->id) . $this->content;
            return true;
        }
        return false;
    }

    private function filterBelowPost() {
        if (isset($this->ads['ads'][$this->id]['abovePost'])) {
            $this->content = $this->content . $this->render();
            return true;
        }
        return false;
        ;
    }

    /**
     * Render ads
     * @return string
     */
    public function render() {
        if (!isset($this->ads['ads'][$this->id]['code'])) {
            return '';
        }
        
        $html = '';
        $args = array('adId' => $this->id, 'adCode' => $this->ads['ads'][$this->id]['code']);
        $output = new \wpquads\template('/includes/vendor/vi/templates/ad', $args);
        $html .= $output->render();
        //$html .= 'test ad' . $this->id;

        return $html;
    }

}

$render = new render();
