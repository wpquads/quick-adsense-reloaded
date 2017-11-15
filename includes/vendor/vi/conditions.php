<?php

namespace wpquads\conditions;

/*
 * vi conditions for WP QUADS
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */


class conditions {

    
        public function __construct() {
            
        }
        
        
        protected function isExcluded(){
            //wp_die( $this->ads);
            return false;
        }
}