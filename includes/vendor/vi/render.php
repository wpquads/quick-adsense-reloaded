<?php

namespace wpquads;

/*
 * vi ad render for WP QUADS used on frontend
 * 
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

class render extends conditions\conditions {

    /**
     * All Ad Settings
     * @var array 
     */
    protected $ads;
    
    /**
     * Array of already populated ads
     * This is to make sure that never 
     * one ad is injected multiple times into page
     * @var array 
     */
    protected $adsInjected = array();
    

    /**
     * Curent ad id
     * @var int
     */
    protected $id;

    /**
     * Filtered Content
     * @var string 
     */
    //protected $content;


    public function __construct() {

        if (is_admin()) {
            return false;
        }
        
        if (!$this->getToken()) {
            return false;
        }

        $this->ads = get_option('quads_vi_ads');

        add_filter('the_content', array($this, 'prepareOutput'), quads_get_load_priority());
    }

    /**
     * Loop through all ads and determine the location of the outputed ads
     * @param string $content
     * @return string
     */
    public function prepareOutput($content) {
        // vi ads are empty
        if (empty($this->ads['ads'])) {
            return $content;
        }


        // Loop through all available ads and use the return value as new $content
        foreach ($this->ads['ads'] as $key => $value) {
            $this->id = $key;
                if(in_array($this->id, $this->adsInjected)){
                    //echo 'skip';
                    // skip. We already injected this ad into site
                    continue;
                }
            $content = $this->filterContent($content);
        }

        return $content;
    }

    /**
     * Loop through all available filter methods
     * New filter can be added by adding methods with prefix 'filter'
     * @param string $content
     * @param int $id
     */
    public function filterContent($content) {

        // Loop through all available filter methods and run them until one of the filters returns sucessfully
        $methods = get_class_methods(get_class());
        //$loop = true;
        foreach ($methods as $method) {

//            if ($loop == false) {
//                break;
//            }
            // Do not use method filterContent()
            if (strpos($method, 'filter') !== false && $method != 'filterContent') {
                // Set content to filtered content
                if (true == ($newContent = $this->$method($content))) {
                    //$loop = false;
                    $this->adsInjected[] = $this->id;
                    return do_shortcode($newContent);
                }
            }
        }
        return do_shortcode($content);
    }

    private function filterNoPost($content) {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] == 'notShown') {
            return $content;
        }
        return false;
    }
    
    private function filterAbovePost($content) {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'abovePost') {
            return $this->render($this->id) . $content;
        }
        return false;
    }

    private function filterBelowPost($content) {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'belowPost') {

            return $content . $this->render();
        }
        return false;
    }

    private function filterMiddlePost($content) {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'middlePost') {

            $paragraphCount = $this->get_paragraph_count($content);
            $middle = round($paragraphCount / 2);
            if ($paragraphCount > 1) {
                $content = explode("</p>", $content);
                array_splice($content, $middle, 0, $this->render()); // splice in at middle position
                $content = implode($content, "</p>");
            }

            return $content . $this->render();
        }
        return false;
    }

    /**
     * Count paragraphs
     * @return int
     */
    private function get_paragraph_count($content) {
        $paragraphs = explode('/p>', $content);
        $paragraphCount = 0;
        if (is_array($paragraphs)) {
            foreach ($paragraphs as $paragraph) {
                if (strlen($paragraph) > 1) {
                    $paragraphCount++;
                }
            }
        }
        return $paragraphCount;
    }

    /**
     * Get the inline style
     * @return string
     */
    private function getInlineStyle() {
        
        // Create margin
        $marginTop = !empty($this->ads['ads'][$this->id]['marginTop']) ? $this->ads['ads'][$this->id]['marginTop'] : '0';
        $marginRight = !empty($this->ads['ads'][$this->id]['marginRight']) ? $this->ads['ads'][$this->id]['marginRight'] : '0';
        $marginBottom = !empty($this->ads['ads'][$this->id]['marginBottom']) ? $this->ads['ads'][$this->id]['marginBottom'] : '0';
        $marginLeft = !empty($this->ads['ads'][$this->id]['marginLeft']) ? $this->ads['ads'][$this->id]['marginLeft'] : '0';
        $margin = 'margin-top:' . $marginTop .'px;margin-right:' . $marginRight .'px;margin-bottom:' . $marginBottom.  'px;margin-left:' . $marginLeft .'px;';
        
        $style = '';
        // Layout Alignment
        if (isset($this->ads['ads'][$this->id]['align']) &&
                $this->ads['ads'][$this->id]['align'] !== 'default') {

            switch ($this->ads['ads'][$this->id]['align']) {
                case 'left':
                    $style .= "float:left;";
                    break;
                case 'right':
                    $style .= "float:right;";
                    break;
                case 'middle':
                    $style .="text-align:center;";
                    break;
            }
        }
        return $style . $margin;
    }
    
        
    /**
     * Check if vi api is active
     * @return boolean
     */
    private function isActive(){
        $isActive = get_option('quads_vi_active');
        if($isActive && $isActive == 'false') {
            return false;
        }
        return true;
    }
    
    private function getToken() {
        $token = get_option('quads_vi_token', '');

        if (empty($token)) {
            return false;
        }

        preg_match("/(\w*).(\w*)/", $token, $output);

        return json_decode(base64_decode($output[2]));
    }

    /**
     * Render ads
     * @return string
     */
    public function render() {

        if ($this->isExcluded() || !$this->isActive()) {
            return '';
        }


        if (!isset($this->ads['ads'][$this->id]['code'])) {
            return '';
        }

        $html = '';
        $args = array(
            'adId' => $this->id,
            'adCode' => $this->ads['ads'][$this->id]['code'],
            'style' => $this->getInlineStyle()
        );
        $output = new \wpquads\template('/includes/vendor/vi/templates/ad', $args);
        $html .= $output->render();

        return $html;
    }

}

$render = new render();
