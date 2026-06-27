<?php

/**
 * The [ntb_num] shortcode handler.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Shortcode
{
    /**
     * Register the shortcode.
     */
    public static function register()
    {
        add_shortcode('ntb_num', [__CLASS__, 'render']);
    }

    /**
     * Normalise shortcode attributes.
     *
     * @param array $atts
     * @return array
     */
    protected static function parse_atts($atts)
    {
        $atts = array_change_key_case((array) $atts, CASE_LOWER);

        return shortcode_atts(
            [
                'value'       => null,
                'format'      => null,
                'prefix'      => '',
                'suffix'      => '',
                'words'       => false,
                'date_format' => 'j F, Y',
                'as_of'       => null,
                'detailed'    => true,
            ],
            $atts,
            'ntb_num'
        );
    }

    /**
     * Render the shortcode output.
     *
     * @param array  $atts
     * @param string $content
     * @param string $tag
     * @return string
     */
    public static function render($atts = [], $content = null, $tag = '')
    {
        $atts = self::parse_atts($atts);

        $value = sanitize_text_field((string) $atts['value']);
        $format = sanitize_key((string) $atts['format']);

        $output = ntb_convert(
            $value,
            $format,
            [
                'words'       => self::to_bool($atts['words']),
                'date_format' => sanitize_text_field((string) $atts['date_format']),
                'as_of'       => $atts['as_of'] !== null ? sanitize_text_field((string) $atts['as_of']) : null,
                'detailed'    => self::to_bool($atts['detailed']),
            ]
        );

        if ($output === false) {
            return '';
        }

        $prefix = sanitize_text_field((string) $atts['prefix']);
        $suffix = sanitize_text_field((string) $atts['suffix']);

        return esc_html($prefix . $output . $suffix);
    }

    /**
     * Coerce a shortcode attribute to a boolean.
     *
     * @param mixed $value
     * @return bool
     */
    protected static function to_bool($value)
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true);
    }
}
