<?php

/**
 * Script dependencies and version for block/build/index.js.
 *
 * WordPress reads this sibling file when registering the block's editorScript
 * so the WordPress packages used by the editor bundle (wp.blocks, wp.element,
 * wp.blockEditor, wp.components, wp.apiFetch, wp.i18n, wp.url) are enqueued and
 * loaded before the script runs.
 */

return array(
    'dependencies' => array(
        'wp-api-fetch',
        'wp-block-editor',
        'wp-blocks',
        'wp-components',
        'wp-element',
        'wp-i18n',
        'wp-url',
    ),
    'version' => '2.0.1',
);
