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
    }

    /**
     * Get AdSense Publisher ID
     * @return boolean
     */
    public function getPublisherID() {
        // loop through all ad fields and check if there is any adsense publisher id
        foreach ($this->settings['ads'] as $key => $value) {
            if (!empty($this->settings['ads'][$key]['g_data_ad_client'])){
                return $this->settings['ads'][$key]['g_data_ad_client'];
            }
        }
        return false;
    }
    
    
    
    public function writeAdsTxt(){
        
        if (!$this->getPublisherID()){
            return false;
        }
        
        $content = 'google.com, ' . $this->getPublisherID() . ', DIRECT f08c47fec0942fa0';       
        $adsTxt = new adsTxt($content, 'f08c47fec0942fa0');
        return $adsTxt->writeAdsTxt();
    }

}
