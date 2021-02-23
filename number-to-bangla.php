<?php
/**
 * Plugin Name: Number to Bangla
 * Plugin URI: 
 * Description: Convert English numbers to Bangla number or Bangla text, Bangla month name and Bangla Money Format. Maximum possible number to covert in Bangla word is 999999999999999.
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: number-to-bangla
 * Author: Md. Rakibul Islam
 * Author URI: https://rakibul.dev
 */

define( 'NTB__PLUGIN_DIR', plugin_dir_path(__FILE__) );

require_once( NTB__PLUGIN_DIR . 'class.banglanumber.php' );

function init_ntb_tags($atts = array(), $tag = '')
{
	$atts = array_change_key_case( (array) $atts, CASE_LOWER );
    return shortcode_atts(
        array(
            'value' => null,
            'format' => null,
        ), $atts
    );
}

function ntb_num( $atts = array(), $content = null, $tag = '' ) 
{
    $ntb_atts = init_ntb_tags($atts,$tag);

    switch( $ntb_atts['format']){
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


function number_to_bangla() 
{
    add_shortcode('ntb_num', 'ntb_num' );
}
 
add_action( 'init', 'number_to_bangla' );