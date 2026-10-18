<?php

namespace BibleSuperSearch\WordPress;

defined('ABSPATH') or die; // exit if accessed directly

/**
 * Relays Bible SuperSearch API requests from the client through the WordPress REST API
 */
class ApiProxy
{
    const ROUTE_NAMESPACE = 'biblesupersearch/v1';

    /**
     * Request params used by the WordPress REST API itself, never relayed to the API
     */
    static protected $reserved_params = [
        'bss_action', 'bss_version', 'rest_route', '_jsonp', '_envelope', '_fields', '_embed', '_method', '_wpnonce', '_locale',
    ];

    static public function registerRoutes()
    {
        // Mirrors the API's own URLs:
        //   /api{/action}            (un-versioned, points to v2 of the API)
        //   /api/{version}{/action}  (versioned, version is v<integer>)
        // Action defaults to 'query'
        register_rest_route(static::ROUTE_NAMESPACE, '/api(?:/(?P<bss_version>v[0-9]+))?(?:/(?P<bss_action>[a-z_]+))?', [
            'methods'  => ['GET', 'POST'],
            'callback' => [static::class, 'handle'],
            // Public, but only to this website's own pages
            'permission_callback' => [static::class, 'permissionCheck'],
        ]);

        // Used by ApiProxyCheck to test whether the REST API is reachable without logging in
        // Does not contact the Bible SuperSearch API
        register_rest_route(static::ROUTE_NAMESPACE, '/proxy_check', [
            'methods'  => 'GET',
            'callback' => function() {
                return ['ok' => TRUE];
            },
            'permission_callback' => [static::class, 'permissionCheck'],
        ]);
    }

    /**
     * URL the client should use as its API URL
     * The client appends 'api' and the rest as needed
     */
    static public function getUrl()
    {
        return trailingslashit(rest_url(static::ROUTE_NAMESPACE));
    }

    /**
     * Refuses requests made by other websites from a visitor's browser
     */
    static public function permissionCheck(\WP_REST_Request $request)
    {
        $allowed = Options::apiProxyOriginAllowed(
            $request->get_header('sec_fetch_site'),
            $request->get_header('origin'),
            [home_url(), site_url()]
        );

        if($allowed) {
            return TRUE;
        }

        return new \WP_Error('rest_forbidden', 'Cross-origin requests are not allowed', ['status' => 403]);
    }

    static public function handle(\WP_REST_Request $request)
    {
        $action  = $request->get_param('bss_action');
        $version = $request->get_param('bss_version');
        $data    = array_merge($request->get_query_params(), $request->get_body_params(), (array) $request->get_json_params());
        $data    = array_diff_key($data, array_flip(static::$reserved_params));

        $Options = Options::getInstance();
        $result  = $Options->apiProxy($action, $data, $version, $request->get_method());

        if($result === FALSE) {
            return new \WP_REST_Response([
                'results'     => [],
                'errors'      => ['Unsupported action'],
                'error_level' => 5,
            ], 404);
        }

        if(!$result['raw']) {
            return new \WP_REST_Response($result['body'], $result['status']);
        }

        // Files are streamed as-is, bypassing the REST API's JSON encoding
        while(ob_get_level()) {
            ob_end_clean();
        }

        if(!$Options->apiStream($result)) {
            return new \WP_REST_Response([
                'results'     => [],
                'errors'      => ['Unable to connect to the Bible SuperSearch API'],
                'error_level' => 5,
            ], 502);
        }

        exit;
    }
}
