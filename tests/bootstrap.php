<?php

/**
 * PHPUnit bootstrap.
 *
 * The converter is framework-free, so we only need a stable timezone and an
 * ABSPATH definition (the converter file guards against direct access).
 */

date_default_timezone_set('UTC');

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

require_once dirname(__DIR__) . '/includes/class-ntb-converter.php';
