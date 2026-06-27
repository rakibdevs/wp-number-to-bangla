<?php

/**
 * Public template helpers, the shared format dispatcher, and developer filters
 * for the "Number to Bangla" plugin.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

if (!function_exists('ntb_clean_affix')) {
    /**
     * Sanitize a prefix/suffix while preserving spaces.
     *
     * sanitize_text_field() trims and collapses whitespace, which drops the
     * trailing space users add to a prefix (e.g. "মোট: "). This strips tags but
     * keeps spacing intact; output is still escaped at render time.
     *
     * @param mixed $value
     * @return string
     */
    function ntb_clean_affix($value)
    {
        return wp_kses((string) $value, []);
    }
}

if (!function_exists('ntb_convert')) {
    /**
     * Central conversion dispatcher shared by the shortcode, block and REST API.
     *
     * @param mixed  $value  The value to convert.
     * @param string $format One of the supported format keys.
     * @param array  $args   Optional per-format options:
     *                       - words (bool)        percentage/time spoken form
     *                       - date_format (string) bnDate output format
     *                       - as_of (string)      reference date for age
     *                       - detailed (bool)     detailed age output
     * @return string|false The converted string, or false on invalid input.
     */
    function ntb_convert($value, $format, $args = [])
    {
        $args = wp_parse_args(
            $args,
            [
                'words'       => false,
                'date_format' => 'j F, Y',
                'as_of'       => null,
                'detailed'    => true,
            ]
        );

        switch ($format) {
            case 'number':
                $output = BanglaNumberConverter::bnNum($value);
                break;
            case 'word':
                $output = BanglaNumberConverter::bnWord($value);
                break;
            case 'money':
                $output = BanglaNumberConverter::bnMoney($value);
                break;
            case 'month':
                $output = BanglaNumberConverter::bnMonth($value);
                break;
            case 'comma':
                $output = BanglaNumberConverter::bnCommaLakh($value);
                break;
            case 'percentage':
                $output = BanglaNumberConverter::bnPercentage($value, (bool) $args['words']);
                break;
            case 'date':
                $output = BanglaNumberConverter::bnDate($value, $args['date_format']);
                break;
            case 'day':
                $output = BanglaNumberConverter::bnDay($value);
                break;
            case 'time':
                $output = BanglaNumberConverter::bnTime($value, (bool) $args['words']);
                break;
            case 'duration':
                $output = BanglaNumberConverter::bnDuration($value);
                break;
            case 'age':
                $output = BanglaNumberConverter::bnAge($value, $args['as_of'], (bool) $args['detailed']);
                break;
            case 'bengali-month':
                $output = BanglaNumberConverter::bnBengaliMonth($value);
                break;
            case 'season':
                $output = BanglaNumberConverter::bnSeason($value);
                break;
            case 'ordinal':
                $output = BanglaNumberConverter::bnOrdinal($value);
                break;
            case 'parse':
                $output = BanglaNumberConverter::parseNum($value);
                break;
            default:
                $output = false;
                break;
        }

        /**
         * Filter the converted output before it is returned.
         *
         * @param string|false $output The converted value (false on failure).
         * @param mixed        $value  The original input value.
         * @param string       $format The requested format.
         * @param array        $args   The resolved arguments.
         */
        return apply_filters('ntb_convert_output', $output, $value, $format, $args);
    }
}

if (!function_exists('ntb_to_number')) {
    /**
     * Convert English digits to Bangla digits.
     *
     * @param int|float|string $value
     * @return string|false
     */
    function ntb_to_number($value)
    {
        return ntb_convert($value, 'number');
    }
}

if (!function_exists('ntb_to_word')) {
    /**
     * Convert a number to Bangla words.
     *
     * @param int|float|string $value
     * @return string|false
     */
    function ntb_to_word($value)
    {
        return ntb_convert($value, 'word');
    }
}

if (!function_exists('ntb_to_money')) {
    /**
     * Convert a number to Bangla currency words.
     *
     * @param int|float|string $value
     * @return string|false
     */
    function ntb_to_money($value)
    {
        return ntb_convert($value, 'money');
    }
}

if (!function_exists('ntb_to_date')) {
    /**
     * Convert a date to a Bangla-formatted date string.
     *
     * @param string|int $value
     * @param string     $format
     * @return string|false
     */
    function ntb_to_date($value, $format = 'j F, Y')
    {
        return ntb_convert($value, 'date', ['date_format' => $format]);
    }
}

if (!function_exists('ntb_ordinal')) {
    /**
     * Convert a number to a Bangla ordinal.
     *
     * @param int|string $value
     * @return string|false
     */
    function ntb_ordinal($value)
    {
        return ntb_convert($value, 'ordinal');
    }
}
