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

    public function __construct() {

        if ($this->debug) {
            // Test endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
//            $this->urlDashboard = 'https://dashboard-test.vi.ai';
//            $this->urlSignup = 'https://dashboard-api-test.vidint.net/v1/api/signup';
//            $this->urlAuthenticate = 'https://dashboard-api-test.vidint.net/v1/api/authenticate';
//            $this->urlRevenue = 'https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue';
//            $this->urlJs = 'https://dashboard-api-test.vidint.net/v1/api/inventory/jstag';
//            $this->urlSignup = 'https://www-test.vi.ai/publisher-registration/';
        } else {
            // Production endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
//            $this->urlDashboard = 'https://dashboard-test.vi.ai';
//            $this->urlSignup = 'https://dashboard-api-test.vidint.net/v1/api/signup';
//            $this->urlAuthenticate = 'https://dashboard-api-test.vidint.net/v1/api/authenticate';
//            $this->urlRevenue = 'https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue';
//            $this->urlJs = 'https://dashboard-api-test.vidint.net/v1/api/inventory/jstag';
//            $this->urlJs = 'https://www-test.vi.ai/publisher-registration/';
        }
        $this->settings();
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
        "settings": {
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
      }
     */
    private function settings() {
        $args = array(
            'method' => 'GET',
            'headers' => array(),
            'timeout' => 45
        );
        $response = wp_remote_post($this->urlSettings, $args);
        if (is_array($response)) {
            $this->settings = json_decode($response['body']);
        }
        return false;
    }

    /**
     * Get vi settings
     * @return obj
     */
    public function getSettings() {
        return $this->settings;
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

}
