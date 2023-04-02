<?php

/**
 * Plugin Name: Number to Bangla
 * Plugin URI: https://wordpress.org/plugins/number-to-bangla/
 * Description: "Number to Bangla" is a WordPress plugin that allows you to convert English numbers to Bangla numbers, Bangla text, Bangla month names, and Bangla money format. It supports numbers up to 999,999,999,999,999 and provides shortcode options to easily display converted numbers on your websit
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: number-to-bangla
 * Author: Md. Rakibul Islam
 * Author URI: https://github.com/rakibdevs
 */

defined('NTB_PLUGIN_DIR') || define('NTB_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(NTB_PLUGIN_DIR . 'class.banglanumber.php');

function init_ntb_tags($atts = [], $tag = '')
{
    $atts = array_change_key_case((array) $atts, CASE_LOWER);
    return shortcode_atts(
        [
            'value' => null,
            'format' => null,
        ],
        $atts
    );
}

function ntb_num($atts = [], $content = null, $tag = '')
{
    $ntb_atts = init_ntb_tags($atts, $tag);

    switch ($ntb_atts['format']) {
        case 'number':
            $output = BanglaNumber::bnNum($ntb_atts['value']);
            break;
        case 'money':
            $output = BanglaNumber::bnMoney($ntb_atts['value']);
            break;
        case 'word':
            $output = BanglaNumber::bnWord($ntb_atts['value']);
            break;
        case 'month':
            $output = BanglaNumber::bnMonth($ntb_atts['value']);
            break;
        case 'comma':
            $output = BanglaNumber::bnCommaLakh($ntb_atts['value']);
            break;
        default:
            $output = '';
            break;
    }

    return $output;
}


function register_number_to_bangla_shortcode()
{
    add_shortcode('ntb_num', 'ntb_num');
}

add_action('init', 'register_number_to_bangla_shortcode');
