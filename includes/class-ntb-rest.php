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
     * Maximum number of items accepted per /batch request.
     */
    const MAX_BATCH_ITEMS = 100;

    /**
     * Hook REST route registration.
     */
    public static function register()
    {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    /**
     * Register the /convert and /batch routes.
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
                        'sanitize_callback' => 'ntb_clean_affix',
                    ],
                    'suffix' => [
                        'default'           => '',
                        'sanitize_callback' => 'ntb_clean_affix',
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

        register_rest_route(
            self::NAMESPACE,
            '/batch',
            [
                'methods'             => 'POST',
                'callback'            => [__CLASS__, 'convert_batch'],
                'permission_callback' => '__return_true',
                'args'                => [
                    'items' => [
                        'required' => true,
                        'type'     => 'array',
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

    /**
     * Handle a batch conversion request: an array of independent
     * {value, format, prefix, suffix, words, date_format} items, converted
     * in one round trip.
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function convert_batch($request)
    {
        $items = $request->get_param('items');

        if (!is_array($items) || empty($items)) {
            return new WP_Error(
                'ntb_invalid_batch',
                __('The "items" parameter must be a non-empty array.', 'number-to-bangla'),
                ['status' => 400]
            );
        }

        if (count($items) > self::MAX_BATCH_ITEMS) {
            return new WP_Error(
                'ntb_batch_too_large',
                sprintf(
                    /* translators: %d: maximum number of batch items allowed. */
                    __('A maximum of %d items is allowed per batch request.', 'number-to-bangla'),
                    self::MAX_BATCH_ITEMS
                ),
                ['status' => 400]
            );
        }

        $results = [];
        foreach ($items as $item) {
            if (!is_array($item) || !isset($item['value'], $item['format'])) {
                $results[] = ['input' => null, 'format' => null, 'output' => false];
                continue;
            }

            $value = sanitize_text_field((string) $item['value']);
            $format = sanitize_key((string) $item['format']);

            $output = ntb_convert(
                $value,
                $format,
                [
                    'words'       => !empty($item['words']),
                    'date_format' => isset($item['date_format']) ? sanitize_text_field((string) $item['date_format']) : 'j F, Y',
                ]
            );

            $prefix = isset($item['prefix']) ? ntb_clean_affix($item['prefix']) : '';
            $suffix = isset($item['suffix']) ? ntb_clean_affix($item['suffix']) : '';

            $results[] = [
                'input'  => $value,
                'format' => $format,
                'output' => $output === false ? false : $prefix . $output . $suffix,
            ];
        }

        return rest_ensure_response(['results' => $results]);
    }
}
