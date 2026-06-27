<?php

/**
 * REST API endpoint: GET /wp-json/ntb/v1/convert
 *
 * Powers the Gutenberg block live preview and enables headless/JS use.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Rest
{
    const NAMESPACE = 'ntb/v1';

    /**
     * Hook REST route registration.
     */
    public static function register()
    {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    /**
     * Register the /convert route.
     */
    public static function register_routes()
    {
        register_rest_route(
            self::NAMESPACE,
            '/convert',
            [
                'methods'             => 'GET',
                'callback'            => [__CLASS__, 'convert'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'value' => [
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'format' => [
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_key',
                    ],
                    'prefix' => [
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'suffix' => [
                        'default'           => '',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'words' => [
                        'default' => false,
                        'type'    => 'boolean',
                    ],
                    'date_format' => [
                        'default'           => 'j F, Y',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
            ]
        );
    }

    /**
     * Handle the conversion request.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function convert($request)
    {
        $value = $request->get_param('value');
        $format = $request->get_param('format');

        $output = ntb_convert(
            $value,
            $format,
            [
                'words'       => (bool) $request->get_param('words'),
                'date_format' => $request->get_param('date_format'),
            ]
        );

        if ($output === false) {
            return new WP_Error(
                'ntb_invalid_input',
                __('Could not convert the given value for the requested format.', 'number-to-bangla'),
                ['status' => 400]
            );
        }

        $prefix = (string) $request->get_param('prefix');
        $suffix = (string) $request->get_param('suffix');

        return rest_ensure_response(
            [
                'input'  => $value,
                'format' => $format,
                'output' => $prefix . $output . $suffix,
            ]
        );
    }
}
