<?php

/**
 * Plugin Name: Number to Bangla
 * Plugin URI: https://wordpress.org/plugins/number-to-bangla/
 * Description: Convert English numbers to Bangla numbers, Bangla words, money, dates, months, seasons, durations, ages, ordinals and more — via shortcode, Gutenberg block, REST API, WP-CLI or template helpers. Supports numbers up to 999,999,999.
 * Version: 2.1.0
 * Requires at least: 5.6
 * Requires PHP: 7.4
 * Tested up to: 7.0.1
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
defined('NTB_VERSION') || define('NTB_VERSION', '2.1.0');

require_once NTB_PLUGIN_DIR . 'includes/class-ntb-converter.php';
require_once NTB_PLUGIN_DIR . 'includes/helpers.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-shortcode.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-rest.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-block.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-clock-block.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-cli.php';
require_once NTB_PLUGIN_DIR . 'includes/class-ntb-elementor.php';

/**
 * Boot the plugin interfaces.
 *
 * Translations are loaded automatically by WordPress (4.6+) since the text
 * domain matches the plugin slug, so no manual load_plugin_textdomain() call
 * is needed.
 */
function ntb_bootstrap()
{
    NTB_Shortcode::register();
    NTB_Rest::register();
    NTB_Block::register();
    NTB_Clock_Block::register();
    NTB_Cli::register();
    NTB_Elementor::register();
}
ntb_bootstrap();
