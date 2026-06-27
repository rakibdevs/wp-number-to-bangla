<?php

/**
 * Plugin Name: Number to Bangla
 * Plugin URI: https://wordpress.org/plugins/number-to-bangla/
 * Description: Convert English numbers to Bangla numbers, Bangla words, money, dates, months, seasons, durations, ages, ordinals and more — via shortcode, Gutenberg block, REST API or template helpers. Supports numbers up to 999,999,999,999,999.
 * Version: 2.0.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 6.5
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: number-to-bangla
 * Domain Path: /languages
 * Author: Md. Rakibul Islam
 * Author URI: https://github.com/rakibdevs
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

defined('NTB_PLUGIN_DIR') || define('NTB_PLUGIN_DIR', plugin_dir_path(__FILE__));
defined('NTB_PLUGIN_FILE') || define('NTB_PLUGIN_FILE', __FILE__);
defined('NTB_VERSION') || define('NTB_VERSION', '2.0.0');

require_once NTB_PLUGIN_DIR . 'includes/class-ntb-converter.php';
require_once NTB_PLUGIN_DIR . 'includes/helpers.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-shortcode.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-rest.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-block.php';

/**
 * Load the plugin text domain for translations.
 */
function ntb_load_textdomain()
{
    load_plugin_textdomain(
        'number-to-bangla',
        false,
        dirname(plugin_basename(NTB_PLUGIN_FILE)) . '/languages'
    );
}
add_action('init', 'ntb_load_textdomain');

/**
 * Boot the plugin interfaces.
 */
function ntb_bootstrap()
{
    NTB_Shortcode::register();
    NTB_Rest::register();
    NTB_Block::register();
}
ntb_bootstrap();
