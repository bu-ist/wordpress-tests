<?php
/**
 * Installs additional MU sites for the purpose of the unit-tests
 *
 * @todo Reuse the init/load code in init.php
 */
error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

$config_file_path = $argv[1];

require_once $config_file_path . '/unittests-config.php';
require_once $config_file_path . '/lib/functions.php';

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = WP_TESTS_DOMAIN;
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

require_once ABSPATH . '/wp-settings.php';

require_once ABSPATH . '/wp-admin/includes/upgrade.php';
require_once ABSPATH . '/wp-includes/wp-db.php';

echo "Installing sites...\n";

if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE ) {
	$blogs = explode(',', WP_TESTS_BLOGS);
	foreach ( $blogs as $blog ) {
		if ( WP_TESTS_SUBDOMAIN_INSTALL ) {
			$newdomain = $blog.'.'.preg_replace( '|^www\.|', '', WP_TESTS_DOMAIN );
			$path = $base;
		} else {
			$newdomain = WP_TESTS_DOMAIN;
			$path = $base.$blog.'/';
		}
		$blog_created = wpmu_create_blog( $newdomain, $path, $blog, email_exists(WP_TESTS_EMAIL) , array( 'public' => 1 ), 1 );
		if( is_wp_error( $blog_created ) ) {
			echo "Blog could not be created." . $blog_created->get_error_message() . "\n";
		} else {
			switch_to_blog($blog_created);	
			if( isset( $wp_tests_plugins ) && is_array( $wp_tests_plugins ) ) {
				echo "Installing site plugins for blog_id: $blog_created...\n";
				wp_test_install_plugins($wp_tests_plugins);
			}

			if( ( defined('WP_TESTS_TEMPLATE') && (WP_TESTS_TEMPLATE != '' ) ) || ( defined( 'WP_TESTS_STYLESHEET' ) && WP_TESTS_STYLESHEET != '' ) ) {
				if(WP_TESTS_TEMPLATE != WP_TESTS_STYLESHEET) {
					switch_theme(WP_TESTS_TEMPLATE, WP_TESTS_STYLESHEET);
				} else {
					switch_theme(WP_TESTS_STYLESHEET, WP_TESTS_STYLESHEET);
				}
			}
			restore_current_blog();
		}	
	}
	
	if( isset( $wp_tests_ms_plugins ) && is_array( $wp_tests_ms_plugins ) ) {
		echo "Installing network plugins...\n";
		wp_test_install_plugins($wp_tests_ms_plugins, true);
	}
}
