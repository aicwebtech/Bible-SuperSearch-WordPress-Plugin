<?php

namespace BibleSuperSearch\WordPress;

defined('ABSPATH') or die; // exit if accessed directly

/**
 * Detects whether this website blocks the REST API for visitors who are not logged in,
 * which would break the API proxy.
 * Runs in the admin only, never while rendering a page.
 */
class ApiProxyCheck
{
    const OPTION_INDEX = 'biblesupersearch_proxy_status';

    // Re-check on the settings page after this many seconds
    const MAX_AGE = 604800; // 7 days

    /**
     * @return array ['state' => 'ok'|'blocked'|'unknown'|null, 'checked' => int, 'detail' => string]
     */
    static public function getStatus()
    {
        $status = get_option(static::OPTION_INDEX);

        if(!is_array($status)) {
            $status = [];
        }

        return array_replace(['state' => NULL, 'checked' => 0, 'detail' => ''], $status);
    }

    static public function isBlocked()
    {
        $status = static::getStatus();
        return $status['state'] == 'blocked';
    }

    static public function run()
    {
        $status = static::_checkAuthenticationFilter();

        if(!$status) {
            $status = static::_checkLoopback();
        }

        $status['checked'] = time();
        update_option(static::OPTION_INDEX, $status);
        return $status;
    }

    static public function runIfStale()
    {
        $status = static::getStatus();

        if($status['checked'] < time() - static::MAX_AGE) {
            $status = static::run();
        }

        return $status;
    }

    /**
     * Checks for plugins / code refusing the REST API to visitors who are not logged in
     * No network request
     */
    static protected function _checkAuthenticationFilter()
    {
        $user_id = get_current_user_id();
        wp_set_current_user(0);
        $result = apply_filters('rest_authentication_errors', NULL);
        wp_set_current_user($user_id);

        if(is_wp_error($result)) {
            return [
                'state'  => 'blocked',
                'detail' => 'The REST API is restricted to logged-in users (' . $result->get_error_code() . ': ' . $result->get_error_message() . ')',
            ];
        }

        return FALSE;
    }

    /**
     * Requests this website's own proxy_check route, without logging in
     * Catches web server and firewall rules
     */
    static protected function _checkLoopback()
    {
        $response = wp_remote_get(ApiProxy::getUrl() . 'proxy_check', [
            'timeout'   => 10,
            'headers'   => ['Sec-Fetch-Site' => 'same-origin'],
            'sslverify' => apply_filters('https_local_ssl_verify', FALSE),
        ]);

        // This website could not request itself, which doesn't mean visitors are blocked
        if(is_wp_error($response)) {
            return [
                'state'  => 'unknown',
                'detail' => 'This website could not connect to itself to test the REST API (' . $response->get_error_message() . ')',
            ];
        }

        $code = (int) wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), TRUE);

        if($code == 200 && is_array($body) && !empty($body['ok'])) {
            return ['state' => 'ok', 'detail' => ''];
        }

        if($code == 200 || in_array($code, [401, 403, 404], TRUE)) {
            return [
                'state'  => 'blocked',
                'detail' => 'The REST API test request returned HTTP ' . $code . ($code == 200 ? ' with an unexpected response' : ''),
            ];
        }

        return [
            'state'  => 'unknown',
            'detail' => 'The REST API test request returned HTTP ' . $code,
        ];
    }

    /**
     * Admin notice on the plugin's settings page
     */
    static public function adminNotice()
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : NULL;

        if(!$screen || strpos($screen->id, 'biblesupersearch') === FALSE || !current_user_can('manage_options')) {
            return;
        }

        $options = Options::getInstance()->getOptions();

        if(empty($options['apiProxy'])) {
            return;
        }

        $status = static::getStatus();

        if($status['state'] == 'blocked') {
            $class = 'notice-error';
            $msg   = 'Bible SuperSearch: the API Proxy is enabled, but this website blocks the WordPress REST API for visitors who are not logged in. '
                   . 'API requests are being sent directly from visitors\' browsers instead. '
                   . 'To use the proxy, allow public access to /wp-json/biblesupersearch/ in your security plugin, firewall or web host settings, then save the Bible SuperSearch settings to re-test.';
        } elseif($status['state'] == 'unknown') {
            $class = 'notice-warning';
            $msg   = 'Bible SuperSearch: unable to verify that the API Proxy is reachable by visitors. '
                   . 'If searches fail, allow public access to /wp-json/biblesupersearch/ or disable the API Proxy on the Advanced tab.';
        } else {
            return;
        }

        if($status['detail']) {
            $msg .= '<br />Details: ' . esc_html($status['detail']);
        }

        echo '<div class="notice ' . esc_attr($class) . '"><p>' . $msg . '</p></div>';
    }
}
