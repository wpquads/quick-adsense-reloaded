<?php

namespace wpquads\conditions;

/*
 * vi conditions for WP QUADS
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

class conditions {

    /**
     * global options
     * @global array $quads_options
     * @return boolean
     */
    protected function isExcluded() {
        global $quads_options;

        /**
         *  Ad specific options
         */

        // User logged in
        if (is_user_logged_in() && ( isset($quads_options['visibility']['AppLogg'])))
            return true;

        // Is frontpage
        if (is_front_page() &&
                isset($this->ads['ads'][$this->id]['excludedExtraPages']) &&
                in_array('homePage', $this->ads['ads'][$this->id]['excludedExtraPages']))
            return true;
        

        if (quads_is_amp_endpoint())
            return true;

        if ($this->isExcludedUserRole())
            return true;

        if ($this->isExcludedPostTypes())
            return true;

        if ($this->isExcludedPageId())
            return true;

        if ($this->isExcludedByMetaKey())
            return true;

        /**
         *  Global options
         */

        // Is feed
        if (is_feed()) {
            return true;
        }
        
        // Is search
        if (is_search()) {
            return true;
        }
        
        // Is 404
        if (is_404()) {
            return true;
        }
        
        // Is category
        if (is_category()) {
            return true;
        }
        // Is archive page
        if (is_archive()) {
            return true;
        }

        // Default
        return false;
    }

    /**
     * Excluded user roles
     * @return boolean
     */
    private function isExcludedUserRole() {

        if (!isset($this->ads['ads'][$this->id]['excludedUserRoles']) ||
                empty($this->ads['ads'][$this->id]['excludedUserRoles'])
        ) {
            return false;
        }

        if (isset($this->ads['ads'][$this->id]['excludedUserRoles']) &&
                count(array_intersect($this->ads['ads'][$this->id]['excludedUserRoles'], wp_get_current_user()->roles)) >= 1) {
            return true;
        }

        return false;
    }

    /**
     * Check if post id is excluded
     * @global array $post
     * @return boolean
     */
    private function isExcludedPageId() {
        global $post;


        if (!isset($post->ID)) {
            return true;
        }

        if (!isset($this->ads['ads'][$this->id]['excludedPostIds']) ||
                empty($this->ads['ads'][$this->id]['excludedPostIds'])
        ) {
            return false;
        }

        if (strpos($this->ads['ads'][$this->id]['excludedPostIds'], ',') !== false) {
            $excluded = explode(',', $this->ads['ads'][$this->id]['excludedPostIds']);
            if (in_array($post->ID, $excluded)) {
                return true;
            }
        }
        if ($post->ID == $this->ads['ads'][$this->id]['excludedPostIds']) {
            return true;
        }

        // default condition
        return false;
    }

    /**
     * Check if ad is allowed on specific post_type
     * 
     * @global array $quads_options
     * @global array $post
     * @return boolean true if post_type is allowed
     */
    private function isExcludedPostTypes() {
        global $post;

        if (!isset($post)) {
            return true;
        }

        if (!isset($this->ads['ads'][$this->id]['excludedPostTypes']) ||
                empty($this->ads['ads'][$this->id]['excludedPostTypes']) ||
                !is_array($this->ads['ads'][$this->id]['excludedPostTypes']) ||
                $this->ads['ads'][$this->id]['excludedPostTypes'] == 'noPostTypes'
        ) {
            return false;
        }

        $current_post_type = get_post_type($post->ID);
        if (in_array($current_post_type, $this->ads['ads'][$this->id]['excludedPostTypes'])) {
            return true;
        }

        return false;
    }

    /**
     * Check if ad is deactivated by wp quads meta settings in post editor
     * @return boolean
     */
    private function isExcludedByMetaKey() {
        global $post;

        if (!isset($post->ID))
            return false;

        $value_arr = get_post_meta($post->ID, '_quads_config_visibility', true);

        $value_key = isset($value_arr['NoAds']) ? $value_arr['NoAds'] : null;

        if (!empty($value_key) && $value_key == 1) {
            return true;
        }
        return false;
    }

}
