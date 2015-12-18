<?php

/**
 * Widget Functions
 *
 * @package     QUADS
 * @subpackage  Functions/Widgets
 * @copyright   Copyright (c) 2015, René Hermenau
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.9.1
 */



/**
 * Register Widgets
 * 
 * @return void
 * @since 0.9.2
 */
add_action( 'widgets_init', function(){
    global $quads_options;
    $amountWidgets = 10;

    for($i=1;$i<=$amountWidgets;$i++) {
        if( ! empty( $quads_options['ad' . $i . '_widget'] ) ) {
            register_widget( 'quads_widgets_' . $i );
        }
    }
});

class quads_widgets_1 extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                $this->adsID = '1';

                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

        /**
         * Create widget
         * 
         * @global array $quads_options
         * @param array $args
         * @param array $instance
         */
	public function widget($args, $instance) {
        global $quads_options;
        extract($args);
            
        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget1

class quads_widgets_2 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
            $this->adsID = '2';
                
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        extract($args);
        
        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {


            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            //if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            //if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget2

class quads_widgets_3 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '3';
                
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget3

class quads_widgets_4 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '4';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget4


class quads_widgets_5 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '5';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {

             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget5

class quads_widgets_6 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '6';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget6

class quads_widgets_7 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '7';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget7

class quads_widgets_8 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '8';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget8

class quads_widgets_9 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '9';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget9

class quads_widgets_10 extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
                
                $this->adsID = '10';
                $this->AdsWidName = sprintf('AdsWidget%d (Quick Adsense Reloaded)',$this->adsID);
		$this->AdsWidID = sanitize_title(str_replace(array('(',')'),'',$this->AdsWidName));
		parent::__construct(
			$this->AdsWidID, // Base ID
			$this->AdsWidName, // Name
			array('description' => __( 'Widget contains ad code', 'quick-adsense-reloaded' ),) // Args
		);
	}

	public function widget($args, $instance) {
        global $quads_options;
        
        extract($args);

        $cont = get_the_content();
        if (strpos($cont, "<!--OffAds-->") === false && strpos($cont, "<!--OffWidget-->") === false && !(is_home() && $quads_options["visibility"]["AppSide"])) {
            
             

            $codetxt = $quads_options['ad' . $this->adsID . '_widget'];

            echo "\n" . "<!-- Quick Adsense Reloaded -->" . "\n";
            if (array_key_exists('before_widget', $args))
                echo $args['before_widget'];

            echo $codetxt;

            if (array_key_exists('after_widget', $args))
                echo $args['after_widget'];
        };
    }

} // class My_Widget10





