<?php

/**
 * Elementor dynamic tag integration.
 *
 * Registers a "Number to Bangla" dynamic tag when Elementor is active. The
 * tag class extends an Elementor base class, so it is only require()'d
 * inside the registration callback — never at plugin bootstrap — to avoid a
 * fatal error on sites without Elementor installed.
 *
 * @package NumberToBangla
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

class NTB_Elementor
{
    /**
     * Hook dynamic tag registration. A no-op unless Elementor fires this
     * action, so it is always safe to call.
     */
    public static function register()
    {
        add_action('elementor/dynamic_tags/register', [__CLASS__, 'register_tags']);
    }

    /**
     * Register the tag with Elementor's dynamic tags manager.
     *
     * @param mixed $dynamic_tags_manager \Elementor\Core\DynamicTags\Manager
     */
    public static function register_tags($dynamic_tags_manager)
    {
        if (!class_exists('\Elementor\Core\DynamicTags\Tag') || !is_object($dynamic_tags_manager)) {
            return;
        }

        require_once NTB_PLUGIN_DIR . 'includes/class-ntb-elementor-tag.php';

        if (!method_exists($dynamic_tags_manager, 'register')) {
            return;
        }

        $dynamic_tags_manager->register(new NTB_Elementor_Tag());
    }
}
