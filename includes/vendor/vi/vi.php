<?php

namespace wpquads;

/*
 * vi integration for WP QUADS
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

/**
 * Main class for wp quads vi integration
 *
 * @author René Hermenau
 */
class vi {

    /**
     * vi settings
     * @var type 
     */
    public $settings;

    /**
     * Debug mode
     * @var bool 
     */
    private $debug = true;

    /**
     * Base64 decoded jwt token
     * @var obj 
     */
    public $token;

    /**
     * vi notices
     * @var array
     */
    private $notices = array();
    

    public function __construct() {
        
        if ($this->debug) {
            // Test endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
        } else {
            // Production endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
        }
        
        $this->hooks();
        
        //$this->setSettings();
        
        $this->settings = get_option('quads_vi_settings');

        $this->getToken();

        $this->createAdsTxt();
        //wp_die(print_r($this->token));
    }
    
    private function hooks(){
        // Check that license is valid once per week
	add_action( 'quads_daily_event', array( $this, 'setSettings' ) );
    }

    /**
     * vi Settings API
     * @return bool
     * See https://docs.vi.ai/
     * {
      "status": "ok",
      "error": null,
      "data": {
      "_id": [string],
      "signupURL": [string],
      "demoPageURL": [string],
      "loginAPI": [string],
      "directSellURL": [string],
      "dashboardURL": [string],
      "IABcategoriesUrl": [string],
      "revenueAPI": [string],
      "adstxtAPI": [string],
      “languages”: [ {“string” : “string”}, ... ]
      "jsTagAPI": [string]
      }
      }
     */
    public function setSettings() {
        $args = array(
            'method' => 'GET',
            'headers' => array(),
            'timeout' => 45
        );
        $response = wp_remote_post($this->urlSettings, $args);
        //wp_die(isset($response['body']));
        if (isset($response['body']) ) { 
            update_option('quads_vi_settings', json_decode($response['body']));
            return true;
        }
        update_option('quads_vi_settings', '');
        return false;
    }


    /**
     * Get vi settings
     * @return obj
     */
    public function getSettings() {
        return get_option('quads_vi_settings');
    }
    

    /**
     * Login to vi account
     * @param string $email
     * @param string $password
     * @return string json
     */
    public function login($email, $password) {
        $args = array(
            'method' => 'POST',
            'headers' => array(),
            'timeout' => 45,
            'body' => array('email' => $email, 'password' => $password)
        );

        $response = wp_remote_post($this->urlAuthenticate, $args);
        if (is_array($response)) {
            return $response['body'];
        }
        return json_encode('Unknown error: Can not retrive vi login information');
    }

    /**
     * 
     * @return string
     */
    public function getDashboard() {
        $response = wp_remote_get($this->urlDashboard);
        if (is_array($response)) {
            return $response['body']; // use the content
        }
        return '';
    }

    /**
     * Can write to the root of WordPress
     * @return boolean
     */
    private function canWriteRoot() {
        if (is_writable(get_home_path())) {
            return true;
        }
        return false;
    }

    /**
     * 
     */
    public function createAdsTxt() {
        $file = ABSPATH . 'ads.txt';

        // Default ads.txt content
        $vi = "vi.ai " . $this->token->publisherId . " DIRECT # 41b5eef6" . "\r\n";
        $vi .= "spotxchange.com, 74964, RESELLER, 7842df1d2fe2db34 # 41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 74964, RESELLER, 7842df1d2fe2db34 # 41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 104684, RESELLER, 7842df1d2fe2db34 # 41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 122515, RESELLER, 7842df1d2fe2db34 # 41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 364193, RESELLER # 41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 369249, RESELLER # 41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 440657, RESELLER # 41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 440673, RESELLER # 41b5eef6" . "\r\n";

        if (!is_file($file)) {
            if (!file_put_contents($file, $vi))
                $this->setNotices("error", "<strong>ADS.TXT couldn't be added</strong><br><br>Important note: WP QUADS hasn't been able to update your ads.txt file. Please make sure to enter the following line manually into <br>" . get_home_path() . "ads.txt:"
                        . "</p><pre>vi.ai " . $this->token->publisherId . " DIRECT </pre> <p>"
                        . "Only by doing so you are able to make more money through video inteligence");
        } else {
            // Remove all vi related entries
            // get everything from ads.txt which already exists
            $content = file($file);

            // Remove any line that contains string # 41b5eef6 mark
            $pattern = "/41b5eef6/i";
            $remove = preg_grep($pattern, $content);
            $content = array_diff($content, $remove);

            // Add the cleaned content
            file_put_contents($file, $content);
            sleep(1);
            // Append the vi related content again
            if (!file_put_contents($file, $vi . PHP_EOL, FILE_APPEND))
                $this->setNotices("error", "<strong>ADS.TXT couldn't be added</strong><br><br>Important note: WP QUADS hasn't been able to update your ads.txt file. Please make sure to enter the following line manually into <br>" . get_home_path() . "ads.txt:"
                        . "</p><pre>vi.ai " . $this->token->publisherId . " DIRECT </pre><p>"
                        . "Only by doing so you are able to make more money through video inteligence");
        }

        $this->setNotices('update-nag', '<strong>ADS.TXT has been added</strong><br><br><strong>WP QUADS</strong> has updated your ads.txt '
                . 'file with lines that declare video inteligence as a legitmate seller of your inventory and enables you to make more money through video inteligence. <a href="https://www.vi.ai/publisher-video-monetization/?utm_source=WordPress&utm_medium=Plugin%20blurb&utm_campaign=wpquads" target="blank" rel="external nofollow">FAQ</a>');
    }

    /**
     * Get the access token
     */
    private function getToken() {
        $token = get_option('quads_vi_token', '');

        if (empty($token)) {
            $this->token = '';
        }

        preg_match("/(\w*).(\w*)/", $token, $output);

        $this->token = json_decode(base64_decode($output[2]));
    }

    /**
     * Collect all available notices
     * @param string $type updated | error | update-nag
     * @param string $message
     */
    private function setNotices($type, $message) {
        $this->notices[] = array('type' => $type, 'message' => $message);
    }

    public function getNotices() {
        return $this->notices;
    }
    
    /**
     * Get total revenue
     * @return mixed string | bool 
     */
    public function getRevenue() {
        $args = array(
            'headers' => array(
            'Authorization' => get_option('quads_vi_token')
            )
        );
        $response = wp_remote_request('https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue', $args);

        if (is_wp_error($response))
            return false;
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401')
            return false;

        // else
        return json_encode($response);
    }

}
