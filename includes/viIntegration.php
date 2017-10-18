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
 * Description of viIntegration
 *
 * @author René Hermenau
 */
class vi {

    /**
     * vi API
     * @var type 
     */
    public $api;
    
    /**
     * Debug mode
     * @var bool 
     */
    private $debug = false;

    public function __construct() {
        $this->debug = true;

        if ($this->debug) {
            // Test endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
            $this->urlDashboard = 'https://dashboard-test.vi.ai';
            $this->urlSignup = 'https://dashboard-api-test.vidint.net/v1/api/signup';
            $this->urlAuthenticate = 'https://dashboard-api-test.vidint.net/v1/api/authenticate';
            $this->urlRevenue = 'https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue';
            $this->urlJs = 'https://dashboard-api-test.vidint.net/v1/api/inventory/jstag';
        } else {
            // Production endpoints
            $this->urlSettings = 'https://dashboard-api-test.vidint.net/v1/api/widget/settings';
            $this->urlDashboard = 'https://dashboard-test.vi.ai';
            $this->urlSignup = 'https://dashboard-api-test.vidint.net/v1/api/signup';
            $this->urlAuthenticate = 'https://dashboard-api-test.vidint.net/v1/api/authenticate';
            $this->urlRevenue = 'https://dashboard-api-test.vidint.net/v1/api/publishers/report/revenue';
            $this->urlJs = 'https://dashboard-api-test.vidint.net/v1/api/inventory/jstag';
        }
    }

    /**
     * Login to vi account
     * @param string $email
     * @param string $password
     * @return string json
     */
    public function login($email, $password) {
        $response = wp_remote_get($this->urlAuthenticate);
        if (is_array($response)) {
            return $response['body'];
        }
        return '';
    }

    public function getApi() {
        return $this->api;
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
     * 
     * @return string
     */
    public function getSettings() {
        $response = wp_remote_get($this->urlSettings);
        if (is_array($response)) {
            return $response['body']; // use the content
        }
        return '';
    }

}
