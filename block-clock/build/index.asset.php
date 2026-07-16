<?php

/**
 * Script dependencies and version for block-clock/build/index.js.
 */

if (!defined('ABSPATH')) {
    exit; // No direct access.
}

return array(
    'dependencies' => array(
        'wp-block-editor',
        'wp-blocks',
        'wp-components',
        'wp-element',
        'wp-i18n',
    ),
    'version' => '2.1.0',
);
