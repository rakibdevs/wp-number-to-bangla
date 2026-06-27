<?php

/**
 * Gutenberg block registration and server-side render.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Block
{
    /**
     * Hook block registration.
     */
    public static function register()
    {
        add_action('init', [__CLASS__, 'register_block']);
    }

    /**
     * Register the block from its block.json metadata.
     */
    public static function register_block()
    {
        if (!function_exists('register_block_type')) {
            return; // WordPress too old for blocks.
        }

        register_block_type(
            NTB_PLUGIN_DIR . 'block',
            ['render_callback' => [__CLASS__, 'render']]
        );
    }

    /**
     * Server-side render callback. Reuses the shared dispatcher so block output
     * matches the shortcode exactly.
     *
     * @param array $attributes
     * @return string
     */
    public static function render($attributes)
    {
        $value = isset($attributes['value']) ? (string) $attributes['value'] : '';
        if ($value === '') {
            return '';
        }

        $output = ntb_convert(
            $value,
            isset($attributes['format']) ? sanitize_key($attributes['format']) : 'number',
            [
                'words'       => !empty($attributes['words']),
                'date_format' => isset($attributes['dateFormat']) ? (string) $attributes['dateFormat'] : 'j F, Y',
            ]
        );

        if ($output === false) {
            return '';
        }

        $prefix = isset($attributes['prefix']) ? ntb_clean_affix($attributes['prefix']) : '';
        $suffix = isset($attributes['suffix']) ? ntb_clean_affix($attributes['suffix']) : '';

        $wrapper = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes()
            : 'class="wp-block-ntb-converter"';

        return sprintf(
            '<span %s>%s</span>',
            $wrapper,
            esc_html($prefix . $output . $suffix)
        );
    }
}
