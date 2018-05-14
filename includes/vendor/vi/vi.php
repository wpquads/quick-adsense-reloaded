<?php

namespace wpquads;

/*
 * vi integration for WP QUADS
 * @author René Hermenau
 * @email info@mashshare.net
 * 
 */

/**
 * Main class for wp quads vi integration used in wp-admin section
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
    private $debug = false;

    /**
     * Use this to force reload of the settings
     * Used after switching to debug and vice versa
     * @var type 
     */
    private $forceReload = false;

    /**
     * Base64 decoded jwt token
     * @var obj 
     */
    public $token;

    /**
     * Available ads
     * @var obj 
     */
    public $ads;

    /**
     * Ad Settings
     * @var obj 
     */
    public $adsSettings;

    public function __construct() {

        if ($this->debug) {
            // Test endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
        } else {
            // Production endpoints
            $this->urlSettings = 'https://dashboard-api.vidint.net/v1/api/widget/settings';
        }


        if ($this->forceReload) {
            $this->setSettings();
        }

        $this->getToken();

        $this->hooks();

        $this->settings = get_option('quads_vi_settings');

        $this->ads = get_option('quads_vi_ads');
    }

    /**
     * Load hooks
     */
    public function hooks() {
        if (is_admin()) {
            // Register the vi ad settings
            add_action('admin_init', array($this, 'registerSettings'));
            add_action('admin_notices', array($this, 'getDebugNotice'));
        }

        // Only run the following actions when vi is activated and user confirmed his registration
        // We need to ensure publishers privacy and do not want to send more personal information than absolutely necessary
        if (!empty($this->token)) {
            // Cron Check vi api settings daily
            add_action('quads_weekly_event', array($this, 'setSettings'));
            add_action('quads_daily_event', array($this, 'setActive'));
            add_action('quads_daily_event', array($this, 'setRevenue'));
            add_action('quads_weekly_event', array($this, 'verifyViAdCode'));
        }

        // Shortcodes
        add_shortcode('quadsvi', array($this, 'getShortcode'));
    }

    /**
     * Write a warning notice when debug mode is on
     */
    public function getDebugNotice() {
        if ($this->debug) {
            echo '<div class="notice notice-error" id="wpquads-adblock-notice" style="">ATTENTION: WP QUADS vi debug mode is activated</div>';
        }
        return false;
    }

    /**
     * Register the vi ad settings
     */
    public function registerSettings() {
        register_setting('quads_settings', 'quads_vi_ads');
    }

    /**
     * Shortcode to include vi ad
     *
     * @param array $atts
     */
    public function getShortcode($atts) {
        global $quads_options;

        if (!$this->token) {
            return;
        }

        if (quads_check_meta_setting('NoAds') === '1') {
            return;
        }

        if (quads_is_amp_endpoint()) {
            return;
        }

        // The ad id
        $id = isset($atts['id']) ? (int) $atts['id'] : 1;

        $viad = $this->getAdCode();

        $style = 'min-width:363px;min-height:363px;';

        $code = "\n" . '<!-- WP QUADS v. ' . QUADS_VERSION . '  Shortcode vi ad -->' . "\n";
        $code .= '<div class="quads-location' . $id . '" id="quads-vi-ad' . $id . '" style="' . $style . '">' . "\n";
        $code .= "<script>";
        $code .= do_shortcode($viad['ads'][1]['code']);
        $code .= '</script>' . "\n";
        $code .= '</div>' . "\n";

        return $code;
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
      "adsTxtAPI": [string],
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


        if (is_wp_error($response)) {
            update_option('quads_vi_api_error', $response->get_error_message() );
            return false;
        } else {
            delete_option('quads_vi_api_error' );
            $response = json_decode($response['body']);
        }


        if (isset($response->status) && $response->status == 'ok' && !empty($response)) {
            update_option('quads_vi_settings', $response);
            return true;
        }
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
     * Get languges
     * @return array
     */
    public function getLanguages() {

        if (!isset($this->settings->data->languages)) {
            return array();
        }

        $languages = array();
        foreach ($this->settings->data->languages as $language) {
            foreach ($language as $key => $value) {
                $languages[$key] = $value;
            }
        }
        if (count($languages) > 0) {
            return $languages;
        } else {
            return array();
        }

        return array();
    }

    /**
     * Get font family
     * @return array
     */
    public function getFontFamily() {
        return array(
            'select' => 'Select Font Family',
            'Arial' => 'Arial',
            'Times New Roman' => 'Times New Roman',
            'Georgia' => 'Georgia',
            'Palatino Linotype' => 'Palatino Linotype',
            'Arial' => 'Arial',
            'Arial Black' => 'Arial Black',
            'Comic Sans MS' => 'Comic Sans MS',
            'Impact' => 'Impact',
            'Lucida Sans Unicode' => 'Lucida Sans Unicode',
            'Tahoma' => 'Tahoma',
            'Trebuchet MS' => 'Trebuchet MS',
            'Verdana' => 'Verdana',
            'Courier New' => 'Courier New',
            'Lucida Console' => 'Lucida Console',
        );
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
     * Create ads.txt
     * @return boolean
     */
    public function createAdsTxt() {

        if (!isset($this->token->publisherId)) {
            return false;
        }

        $file = ABSPATH . 'ads.txt';

        // Default ads.txt content used when api is not returning anything
        $vi = "vi.ai " . $this->token->publisherId . " DIRECT #41b5eef6" . "\r\n";
        $vi .= "spotxchange.com, 74964, RESELLER, 7842df1d2fe2db34 #41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 74964, RESELLER, 7842df1d2fe2db34 #41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 104684, RESELLER, 7842df1d2fe2db34 #41b5eef6" . "\r\n";
        $vi .= "spotx.tv, 122515, RESELLER, 7842df1d2fe2db34 #41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 364193, RESELLER #41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 369249, RESELLER #41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 440657, RESELLER #41b5eef6" . "\r\n";
        $vi .= "freewheel.tv, 440673, RESELLER #41b5eef6" . "\r\n";

        // Try to get ads.txt content from vi api
        if (false !== ( $adcode = $this->getAdsTxtContent() )) {
            $vi = $adcode;
        }

        $adsTxt = new \wpquads\adsTxt($vi, '41b5eef6');
        return $adsTxt->writeAdsTxt();
    }

    /**
     * Get ads.txt from vi api
     * @return mixed string | bool 
     */
    public function getAdsTxtContent() {
        $vi_token = get_option('quads_vi_token');
        if (!$vi_token)
            return false;


        $args = array(
            'headers' => array(
                'Authorization' => $vi_token
            )
        );
        $response = wp_remote_request($this->settings->data->adsTxtAPI, $args);

        if (is_wp_error($response))
            return false;
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401')
            return false;
        if (empty($response))
            return false;

        // convert into object
        $response = json_decode($response['body']);

        if (!isset($response->status) || $response->status !== 'ok') {
            return false;
        }

        // else
        return $response->data;
    }

    /**
     * Get the access token
     */
    private function getToken() {
        $token = get_option('quads_vi_token', '');

        if (empty($token)) {
            $this->token = '';
            return false;
        }

        preg_match("/(\w*).(\w*)/", $token, $output);

        $this->token = json_decode(base64_decode($output[2]));
    }

    public function getAds() {
        $this->ads = get_option('quads_vi_ads');
    }

    public function setAds() {
        update_option('quads_vi_ads', $this->ads);
    }

    /**
     * Get revenue from API and store it in db
     * @return mixed string | bool 
     */
    public function setRevenue() {
        $vi_token = get_option('quads_vi_token');
        if (!$vi_token)
            return false;

        if (!isset($this->settings->data->revenueAPI))
            return false;


        $args = array(
            'headers' => array(
                'Authorization' => $vi_token
            )
        );
        //$response = wp_remote_request('https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue', $args);
        $response = wp_remote_request($this->settings->data->revenueAPI, $args);

        if (is_wp_error($response))
            return false;
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401')
            return false;
        if (empty($response))
            return false;

        // convert into object
        $response = json_decode($response['body']);

        if (!isset($response->status) || $response->status !== 'ok') {
            return false;
        }

        // else
        //return $response->data;
        update_option('quads_vi_revenue', $response->data);
        return true;
    }

    /**
     * Get Revenue from db
     * @return object
     */
    public function getRevenue() {
        return get_option('quads_vi_revenue');
    }

    /**
     * Get ad code from api and store it in database
     * @return mixed string | bool 
     */
    public function setAdCode() {
        $vi_token = get_option('quads_vi_token');

        $ads = get_option('quads_vi_ads');

        if (!$vi_token){
            error_log('vi token is empty');
            return false;
        }


        $viParam = $this->getViAdParams($ads);
//        $viParam = array(
//                'domain' => $this->getDomain(),
//                //'adUnitType' => 'FLOATING_OUTSTREAM',
//                'adUnitType' => 'NATIVE_VIDEO_UNIT',
//                'divId' => 'div_id',
//                'language' => isset($ads['ads'][1]['language']) ? $ads['ads'][1]['language'] : 'en-en',
//                'iabCategory' => isset($ads['ads'][1]['iab2']) && 'select' != $ads['ads'][1]['iab2'] ? $ads['ads'][1]['iab2'] : 'IAB2-16',
//                'font' => !empty($ads['ads'][1]['txt_font_family']) && $ads['ads'][1]['txt_font_family'] != 'select' ? $ads['ads'][1]['txt_font_family'] : 'Verdana',
//                'fontSize' => !empty($ads['ads'][1]['font_size']) ? $ads['ads'][1]['font_size'] : '12',
//                'keywords' => !empty($ads['ads'][1]['keywords']) ? $ads['ads'][1]['keywords'] : 'key,words',
//                'textColor' => !empty($ads['ads'][1]['text_color']) ? '#' . $ads['ads'][1]['text_color'] : '#00ff00',
//                'backgroundColor' => !empty($ads['ads'][1]['bg_color']) ? '#' . $ads['ads'][1]['bg_color'] : '#00ff00',
//                'vioptional1' => isset($ads['ads'][1]['optional1']) ? $ads['ads'][1]['optional1'] : 'optional1',
//                'vioptional2' => isset($ads['ads'][1]['optional2']) ? $ads['ads'][1]['optional2'] : 'optional2',
//                'vioptional3' => isset($ads['ads'][1]['optional3']) ? $ads['ads'][1]['optional3'] : 'optional3',
//                'float' => true,
//                'logoUrl' => 'http://url.com/logo.jpg',
//                'dfpSupport' => true,
//                'sponsoredText' => 'Sponsored text',
//                'poweredByText' => 'Powered by VI'
//            );

        $args = array(
            'method' => 'POST',
            'timeout' => 15,
            'headers' => array(
                'Authorization' => $vi_token,
                'Content-Type' => 'application/json; charset=utf-8'
            ),
            'body' => json_encode($viParam)
        );

        $response = wp_remote_post($this->settings->data->jsTagAPI, $args);
        
        //wp_die(json_encode($response));


        if (is_wp_error($response)){
            error_log('is wp error: ' . $response);
            return false;
        }
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401'){
            error_log('is 404 or 401! Endpoint: ' . $this->settings->data->jsTagAPI . ' Token: '. $vi_token . ' Response: ' . print_r($response, true) . ' Params: ' . print_r($viParam, true));
            // convert into object
            $response = json_decode($response['body']);
            return json_encode($response);
            //return false;
        }
        if (empty($response)){
            error_log('is empty');
            return false;
        }

        // convert into object
        $response = json_decode($response['body']);


        // Die()
        if ($response->status !== 'ok' || empty($response->data)) {
         error_log( 'is ok ' . $response );
         return json_encode($response);
        }

        // Add ad code to key 1 as long as there are no more vi ad codes
        // Later we need to loop through the $ads array to store values
        $ads['ads'][1]['code'] = $response->data;

        //return $response->data;
        update_option('quads_vi_ads', $ads);

        //return $response->data;
        return json_encode($response);
    }

    /**
     * Build ad parameter dynamically
     * @return array
     */
    private function getViAdParams($ads) {

        if (!empty($ads['ads'][1]['font_size'])) {
            return array(
                'domain' => $this->getDomain(),
                //'adUnitType' => 'FLOATING_OUTSTREAM',
                'adUnitType' => 'NATIVE_VIDEO_UNIT',
                'divId' => 'div_id',
                'language' => isset($ads['ads'][1]['language']) ? $ads['ads'][1]['language'] : 'en-en',
                'iabCategory' => isset($ads['ads'][1]['iab2']) && 'select' != $ads['ads'][1]['iab2'] ? $ads['ads'][1]['iab2'] : 'IAB2-16',
                'font' => !empty($ads['ads'][1]['txt_font_family']) && $ads['ads'][1]['txt_font_family'] != 'select' ? $ads['ads'][1]['txt_font_family'] : 'Verdana',
                'fontSize' => !empty($ads['ads'][1]['font_size']) ? $ads['ads'][1]['font_size'] : '12',
                'keywords' => !empty($ads['ads'][1]['keywords']) ? $ads['ads'][1]['keywords'] : 'key,words',
                'textColor' => !empty($ads['ads'][1]['text_color']) ? '#' . $ads['ads'][1]['text_color'] : '#000000',
                'backgroundColor' => !empty($ads['ads'][1]['bg_color']) ? '#' . $ads['ads'][1]['bg_color'] : '#ffffff',
                'vioptional1' => isset($ads['ads'][1]['optional1']) ? $ads['ads'][1]['optional1'] : 'optional1',
                'vioptional2' => isset($ads['ads'][1]['optional2']) ? $ads['ads'][1]['optional2'] : 'optional2',
                'vioptional3' => isset($ads['ads'][1]['optional3']) ? $ads['ads'][1]['optional3'] : 'optional3',
                'float' => true,
                'logoUrl' => 'http://url.com/logo.jpg',
                'dfpSupport' => true,
                'sponsoredText' => 'Sponsored text',
                'poweredByText' => 'Powered by VI'
            );
        } else {
            return array(
                'domain' => $this->getDomain(),
                //'adUnitType' => 'FLOATING_OUTSTREAM',
                'adUnitType' => 'NATIVE_VIDEO_UNIT',
                'divId' => 'div_id',
                'language' => isset($ads['ads'][1]['language']) ? $ads['ads'][1]['language'] : 'en-en',
                'iabCategory' => isset($ads['ads'][1]['iab2']) && 'select' != $ads['ads'][1]['iab2'] ? $ads['ads'][1]['iab2'] : 'IAB2-16',
                'font' => !empty($ads['ads'][1]['txt_font_family']) && $ads['ads'][1]['txt_font_family'] != 'select' ? $ads['ads'][1]['txt_font_family'] : 'Verdana',
                'keywords' => !empty($ads['ads'][1]['keywords']) ? $ads['ads'][1]['keywords'] : 'key,words',
                'textColor' => !empty($ads['ads'][1]['text_color']) ? '#' . $ads['ads'][1]['text_color'] : '#000000',
                'backgroundColor' => !empty($ads['ads'][1]['bg_color']) ? '#' . $ads['ads'][1]['bg_color'] : '#ffffff',
                'vioptional1' => isset($ads['ads'][1]['optional1']) ? $ads['ads'][1]['optional1'] : 'optional1',
                'vioptional2' => isset($ads['ads'][1]['optional2']) ? $ads['ads'][1]['optional2'] : 'optional2',
                'vioptional3' => isset($ads['ads'][1]['optional3']) ? $ads['ads'][1]['optional3'] : 'optional3',
                'float' => true,
                'logoUrl' => 'http://url.com/logo.jpg',
                'dfpSupport' => true,
                'sponsoredText' => 'Sponsored text',
                'poweredByText' => 'Powered by VI'
            );
        }
    }

    public function getAdCode() {
        return get_option('quads_vi_ads');
    }

    /**
     * Get Publisher ID
     * @return string
     */
    public function getPublisherId() {

        return isset($this->token->publisherId) ? $this->token->publisherId : '';
    }

    // Domain
    public function getDomain() {

        $url = parse_url(get_bloginfo('url'));
        $domain = isset($url['host']) ? $url['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $result)) {
            return $result['domain'];
        }
        // Else
        $domainNew = str_replace('www.', '', get_home_url());
        $domainNew = str_replace('https://', '', $domainNew);
        $domainNew = str_replace('http://', '', $domainNew);
        return $domainNew;
    }

    /**
     *  Daily check to make sure the vi API is available
     *  No personal data is logged nor transfered to any other party
     *  All referals and ip adresses are anonymized server internally
     *  @return bool
     */
    public function setActive() {
        $url = 'https://wpquads.com/vi.html';
        $args = array(
            'method' => 'GET',
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8'
            ),
            'body' => ''
        );

        $response = wp_remote_post($url, $args);

        // set active per default
        if (is_wp_error($response)) {
            delete_option('quads_vi_active');
            return true;
        }
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401') {
            delete_option('quads_vi_active');
            return true;
        }
        if (empty($response)) {
            delete_option('quads_vi_active');
            return true;
        }

        if ($response['body'] == 'true') {
            delete_option('quads_vi_active');
            return true;
        }

        // vi is deactivated
        if ($response['body'] == 'false') {
            update_option('quads_vi_active', 'false');
            return false;
        }
    }

    /**
     *  Weekly check to ensure vi ad code has not been changed unintentionally
     *  @return bool
     */
    public function verifyViAdCode() {

        //$url = 'https://wpquads.com/wpquads-api/signup/create.php?domain='.$this->getDomain().'&hash=' . $this->getHash();
        $url = 'https://wpquads.com/wpquads-api/signup/create.php';
        $args = array(
            'method' => 'POST',
            'timeout' => 15,
            'headers' => array(
                'Content-Type' => 'application/json; charset=utf-8'
            ),
            'body' => json_encode(array(
                'domain' => $this->getDomain(),
                'hash' => $this->getHash(),
            ))
        );

        $response = wp_remote_post($url, $args);

        // set active per default
        if (is_wp_error($response)) {
            return true;
        }
        if (wp_remote_retrieve_response_code($response) == '404' || wp_remote_retrieve_response_code($response) == '401') {
            return true;
        }
        if (empty($response)) {
            return true;
        }

        // vi is deactivated
        if ($response['body'] == 'false') {
            return false;
        }
    }

    /**
     * Create a hash to ensure that ad code has not been manippulated or changed unintentionally
     * Use hashing instead sending sensitive publisher data back and forth
     */
    private function getHash() {
        $string = get_option('quads_vi_ads');

        if (isset($string['ads'][1]['code'])) {
            return md5($string['ads'][1]['code']);
        }
        return '';
    }

    /**
     * Get login URL
     * @return string
     */
    public function getLoginURL() {
        if (isset($this->settings->data->loginAPI) &&
                !empty($this->settings->data->loginAPI)) {
            return $this->settings->data->loginAPI;
        }
        return '';
    }

}
