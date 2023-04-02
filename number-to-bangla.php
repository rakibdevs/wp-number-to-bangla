<?php

/**
 * Plugin Name: Number to Bangla
 * Plugin URI: https://wordpress.org/plugins/number-to-bangla/
 * Description: "Number to Bangla" is a WordPress plugin that allows you to convert English numbers to Bangla numbers, Bangla text, Bangla month names, and Bangla money format. It supports numbers up to 999,999,999,999,999 and provides shortcode options to easily display converted numbers on your website
 * Version: 1.1.0
 * License: GPLv2 or later
 * Text Domain: number-to-bangla
 * Author: Md. Rakibul Islam
 * Author URI: https://github.com/rakibdevs
 */

defined('NTB_PLUGIN_DIR') || define('NTB_PLUGIN_DIR', plugin_dir_path(__FILE__));

require_once(NTB_PLUGIN_DIR . 'class.banglaNumberConverter.php');

function init_ntb_tags($atts = [], $tag = '')
{
    $atts = array_change_key_case($atts, CASE_LOWER);
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
    $ntbAttributes = init_ntb_tags($atts, $tag);
    $shortCodeValue = $ntbAttributes['value'];

    switch ($ntbAttributes['format']) {
        case 'number':
            $output = BanglaNumberConverter::bnNum($shortCodeValue);
            break;
        case 'money':
            $output = BanglaNumberConverter::bnMoney($shortCodeValue);
            break;
        case 'word':
            $output = BanglaNumberConverter::bnWord($shortCodeValue);
            break;
        case 'month':
            $output = BanglaNumberConverter::bnMonth($shortCodeValue);
            break;
        case 'comma':
            $output = BanglaNumberConverter::bnCommaLakh($shortCodeValue);
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
