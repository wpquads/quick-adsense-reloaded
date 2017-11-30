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
     * @return string
     */
    public function getPublisherID() {
        // loop through all adsense g_data_ad_client fields and check if there is any adsense publisher id
        foreach ($this->settings['ads'] as $key => $value) {
            if (!empty($value['g_data_ad_client'])){
                return $value['g_data_ad_client'];
            }
        }
        
        // Loop through all other possible ad codes and check if there is any possible google publisher id
        $quads_options = $this->settings;
        
        foreach ($quads_options as $id => $ads) {
            if (!is_array($ads)) {
                continue;
            }
            foreach ($ads as $key => $value) {
                if (is_array($value) && array_key_exists('code', $value) && !empty($value['code'])) {

                    // Check to see if it is google ad
                    if (preg_match('/googlesyndication.com/', $value['code'])) {

                        // Test if its google asyncron ad
                        if (preg_match('/data-ad-client=/', $value['code'])) {
                            //*** GOOGLE ASYNCRON *************
                            //get g_data_ad_client
                            $explode_ad_code = explode('data-ad-client', $value['code']);
                            preg_match('#"([a-zA-Z0-9-\s]+)"#', $explode_ad_code[1], $matches_add_client);
                            return str_replace(array('"', ' '), array(''), $matches_add_client[1]);
                        } else {
                            //*** GOOGLE SYNCRON *************
                            //get g_data_ad_client
                            $explode_ad_code = explode('google_ad_client', $value['code']);
                            preg_match('#"([a-zA-Z0-9-\s]+)"#', $explode_ad_code[1], $matches_add_client);
                            return str_replace(array('"', ' '), array(''), $matches_add_client[1]);
                        }
                    }
                }
            }
        }

        return '';
    }
    

    /**
     * Write ads.txt
     * @return boolean
     */
    public function writeAdsTxt(){
        
        $publisherId = $this->getPublisherID();
        
        if (empty($publisherId)){
            return false;
        }
        
        $content = 'google.com, ' . str_replace('ca-', '', $this->getPublisherID()) . ', DIRECT, f08c47fec0942fa0';       
        $adsTxt = new adsTxt($content, 'f08c47fec0942fa0');
        return $adsTxt->writeAdsTxt();
    }

}
