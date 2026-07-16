<?php

/**
 * "Bangla Clock" block registration and server-side render.
 *
 * Server-renders the current time (site timezone) as the initial markup;
 * block-clock/build/view.js then ticks it client-side every second using the
 * visitor's own browser clock.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Clock_Block
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
            NTB_PLUGIN_DIR . 'block-clock',
            ['render_callback' => [__CLASS__, 'render']]
        );
    }

    /**
     * Server-side render callback.
     *
     * @param array $attributes
     * @return string
     */
    public static function render($attributes)
    {
        $showSeconds = !empty($attributes['showSeconds']);

        try {
            $dt = new DateTime('now', new DateTimeZone(wp_timezone_string()));
        } catch (Exception $e) {
            $dt = new DateTime('now');
        }

        $parts = explode(':', $dt->format($showSeconds ? 'H:i:s' : 'H:i'));
        foreach ($parts as &$part) {
            $part = BanglaNumberConverter::bnNum($part);
        }
        unset($part);
        $display = implode(':', $parts);

        $wrapper = function_exists('get_block_wrapper_attributes')
            ? get_block_wrapper_attributes([
                'class'             => 'ntb-clock',
                'data-show-seconds' => $showSeconds ? '1' : '0',
            ])
            : sprintf(
                'class="wp-block-ntb-clock ntb-clock" data-show-seconds="%s"',
                $showSeconds ? '1' : '0'
            );

        return sprintf('<span %s>%s</span>', $wrapper, esc_html($display));
    }
}
