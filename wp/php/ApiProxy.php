<?php

namespace BibleSuperSearch\WordPress;

defined('ABSPATH') or die; // exit if accessed directly

/**
 * Relays Bible SuperSearch API requests from the client through the WordPress REST API
 */
class ApiProxy 
{
    const ROUTE_NAMESPACE = 'biblesupersearch/v1';

    static public function registerRoutes()
    {
        // Mirrors the API's own URLs:
        //   /api{/action}            (un-versioned, points to v2 of the API)
        //   /api/{version}{/action}  (versioned, version is v<integer>)
        // Action defaults to 'query'
        register_rest_route(static::ROUTE_NAMESPACE, '/api(?:/(?P<bss_version>v[0-9]+))?(?:/(?P<bss_action>[a-z_]+))?', [
            'methods'  => ['GET', 'POST'],
            'callback' => [static::class, 'handle'],
            // Public: used by the front end
            'permission_callback' => '__return_true',
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

    static public function handle(\WP_REST_Request $request)
    {
        $action  = $request->get_param('bss_action');
        $version = $request->get_param('bss_version');
        $data    = array_merge($request->get_query_params(), $request->get_body_params());
        unset($data['bss_action'], $data['bss_version'], $data['rest_route']);

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

        // Files are passed through as-is, bypassing the REST API's JSON encoding
        while(ob_get_level()) {
            ob_end_clean();
        }

        status_header($result['status']);

        if(!isset($result['headers']['content-type'])) {
            $result['headers']['content-type'] = 'application/octet-stream';
        }

        foreach($result['headers'] as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $result['body'];
        exit;
    }
}
