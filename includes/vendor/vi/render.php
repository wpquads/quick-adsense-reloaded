<?php

namespace wpquads;

/*
 * vi ad render for WP QUADS used on front page
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
     * Curent ad id
     * @var int
     */
    protected $id;

    /**
     * Filtered Content
     * @var string 
     */
    protected $content;


    public function __construct() {

        if (is_admin()) {
            return false;
        }

        $this->ads = get_option('quads_vi_ads');

        add_filter('the_content', array($this, 'prepareOutput'), quads_get_load_priority());
    }

    /**
     * Loop through all ads
     * @param string $content
     * @return string
     */
    public function prepareOutput($content) {

        if (empty($this->ads['ads'])) {
            return $content;
        }

        If (empty($this->content)) {
            $this->content = $content;
        }

        // Loop through all available ads and use the return value as new $content
        foreach ($this->ads['ads'] as $key => $value) {
            $this->id = $key;
            $this->content = $this->filterContent();
        }

        return $this->content;
    }

    /**
     * Loop through all available filter methods
     * New filter can be added by adding methods with prefix 'filter'
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

    private function filterNoPost() {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'notShown') {
            $this->content = $this->content;
            return true;
        }
        return false;
    }
    
    private function filterAbovePost() {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'abovePost') {
            $this->content = $this->render($this->id) . $this->content;
            return true;
        }
        return false;
    }

    private function filterBelowPost() {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'belowPost') {

            $this->content = $this->content . $this->render();
            return true;
        }
        return false;
    }

    private function filterMiddlePost() {
        if (isset($this->ads['ads'][$this->id]['position']) &&
                $this->ads['ads'][$this->id]['position'] === 'middlePost') {

            $paragraphCount = $this->get_paragraph_count();
            $middle = round($paragraphCount / 2);
            if ($paragraphCount > 1) {
                $content = explode("</p>", $this->content);
                array_splice($content, $middle, 0, $this->render()); // splice in at middle position
                $this->content = implode($content, "</p>");
            }

            $this->content = $this->content . $this->render();
            return true;
        }
        return false;
    }

    /**
     * Count paragraphs
     * @return int
     */
    private function get_paragraph_count() {
        $paragraphs = explode('/p>', $this->content);
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

    private function getInlineStyle() {
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
        return $style;
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
