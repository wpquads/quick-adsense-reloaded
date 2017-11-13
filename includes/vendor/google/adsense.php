<?php

namespace wpquads;

/*
 * Google AdSense integration for WP QUADS
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

/**
 * Main class for wp quads google adsense integration
 *
 * @author René Hermenau
 */
class adsense {

    /**
     * Settings
     * @var array
     */
    private $settings;

    public function __construct($settings) {
        $this->settings = $settings;
        
        wp_die($this->getPublisherID());
    }

    /**
     * 
     * @return boolean
     */
    private function getPublisherID() {
        // loop through all ad fields and check if there is any adsense publisher id
        foreach ($this->settings['ads'] as $key => $value) {
            if (!empty($this->settings['ads'][$key]['g_data_ad_client'])){
                return $this->settings['ads'][$key]['g_data_ad_client'];
            }
        }
        return false;
    }

}
