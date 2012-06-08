<?php
/**
 * Installs WordPress for running the tests and loads WordPress and the test libraries
 */

error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

require_once 'PHPUnit/Autoload.php';

define('WP_TESTS_PATH', dirname( __FILE__ ) );

require WP_TESTS_PATH . '/lib/functions.php';
	
$config_file_path = WP_TESTS_PATH . '/unittests-config.php';

/*
 * Globalize some WordPress variables, because PHPUnit loads this file inside a function
 * See: https://github.com/sebastianbergmann/phpunit/issues/325
 *
 * These are not needed for WordPress 3.3+, only for older versions
*/
global $table_prefix, $wp_embed, $wp_locale, $_wp_deprecated_widgets_callbacks, $wp_widget_factory;

// These are still needed
global $wpdb, $current_site, $current_blog, $wp_rewrite, $shortcode_tags, $wp;

require_once $config_file_path;

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = WP_TESTS_DOMAIN;
$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

// install WordPress
system( 'php '.escapeshellarg( WP_TESTS_PATH . '/bin/install.php' ) . ' ' . escapeshellarg( WP_TESTS_PATH ) );

// Load the basics part of WordPress.
require_once ABSPATH . '/wp-settings.php';

require WP_TESTS_PATH . '/lib/testcase.php';
require WP_TESTS_PATH . '/lib/exceptions.php';

wp_test_load_plugin_tests();
