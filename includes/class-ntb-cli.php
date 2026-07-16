<?php

/**
 * WP-CLI command: `wp ntb convert <value> --format=<format>`.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Cli
{
    /**
     * Register the command, only when running under WP-CLI.
     */
    public static function register()
    {
        if (!defined('WP_CLI') || !WP_CLI) {
            return;
        }

        WP_CLI::add_command('ntb convert', [__CLASS__, 'convert']);
    }

    /**
     * Convert a value to Bangla.
     *
     * ## OPTIONS
     *
     * <value>
     * : The value to convert.
     *
     * --format=<format>
     * : One of: number, word, money, comma, percentage, month, bengali-month,
     *   season, day, date, bengali-date, week, time, duration, age, ordinal, parse.
     *
     * [--words]
     * : Spell out the value in words (percentage and time formats only).
     *
     * [--prefix=<prefix>]
     * : Text prepended to the output.
     *
     * [--suffix=<suffix>]
     * : Text appended to the output.
     *
     * [--date-format=<format>]
     * : PHP date() tokens for the date/bengali-date formats. Default: "j F, Y".
     *
     * [--as-of=<date>]
     * : Reference date for the age format. Default: now.
     *
     * ## EXAMPLES
     *
     *     wp ntb convert 111 --format=word
     *     wp ntb convert 2024-01-15 --format=bengali-date
     *     wp ntb convert 1345.50 --format=money --prefix="মোট: "
     *
     * @when after_wp_load
     *
     * @param array $args
     * @param array $assoc_args
     */
    public static function convert($args, $assoc_args)
    {
        $value = isset($args[0]) ? $args[0] : '';
        $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'number';

        $output = ntb_convert(
            $value,
            $format,
            [
                'words'       => isset($assoc_args['words']),
                'date_format' => isset($assoc_args['date-format']) ? $assoc_args['date-format'] : 'j F, Y',
                'as_of'       => isset($assoc_args['as-of']) ? $assoc_args['as-of'] : null,
            ]
        );

        if ($output === false) {
            WP_CLI::error('Could not convert the given value for the requested format.');
            return;
        }

        $prefix = isset($assoc_args['prefix']) ? $assoc_args['prefix'] : '';
        $suffix = isset($assoc_args['suffix']) ? $assoc_args['suffix'] : '';

        WP_CLI::line($prefix . $output . $suffix);
    }
}
