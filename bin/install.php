<?php
/**
 * Installs WordPress for the purpose of the unit-tests
 *
 * @todo Reuse the init/load code in init.php
 */
error_reporting( E_ALL & ~E_DEPRECATED & ~E_STRICT );

$config_file_path = $argv[1];

define( 'WP_INSTALLING', true );
require_once $config_file_path . '/unittests-config.php';
require_once $config_file_path . '/lib/functions.php';

$_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.1';
$_SERVER['HTTP_HOST'] = WP_TESTS_DOMAIN;
$PHP_SELF = $GLOBALS['PHP_SELF'] = $_SERVER['PHP_SELF'] = '/index.php';

require_once ABSPATH . '/wp-settings.php';

require_once ABSPATH . '/wp-admin/includes/upgrade.php';
require_once ABSPATH . '/wp-includes/wp-db.php';

$wpdb->suppress_errors();
$wpdb->hide_errors();

$wpdb->query( 'SET storage_engine = INNODB;' );
$wpdb->query( 'DROP DATABASE IF EXISTS '.DB_NAME.";" );
$wpdb->query( 'CREATE DATABASE '.DB_NAME.";" );
$wpdb->select( DB_NAME, $wpdb->dbh );

echo "Installing WordPress...\n";
wp_install( WP_TESTS_TITLE, 'admin', WP_TESTS_EMAIL, true, '', 'a' );

if ( defined('WP_ALLOW_MULTISITE') && WP_ALLOW_MULTISITE ) {
	echo "Installing network…\n";

	define( 'WP_INSTALLING_NETWORK', true );
	//wp_set_wpdb_vars();
	// We need to create references to ms global tables to enable Network.
	foreach ( $wpdb->tables( 'ms_global' ) as $table => $prefixed_table )
		$wpdb->$table = $prefixed_table;
	install_network();
	$result = populate_network(1, WP_TESTS_DOMAIN, WP_TESTS_EMAIL, WP_TESTS_NETWORK_TITLE, '/', WP_TESTS_SUBDOMAIN_INSTALL);

	system( 'php '.escapeshellarg( dirname( __FILE__ ) . '/ms-install.php' ) . ' ' . escapeshellarg( $config_file_path ) );
}

if( isset( $wp_tests_plugins ) && is_array( $wp_tests_plugins ) ) {
	echo "Installing site plugins...\n";
	wp_test_install_plugins($wp_tests_plugins);
}

if( ( defined('WP_TESTS_TEMPLATE') && (WP_TESTS_TEMPLATE != '' ) ) || ( defined( 'WP_TESTS_STYLESHEET' ) && WP_TESTS_STYLESHEET != '' ) ) {
	if(WP_TESTS_TEMPLATE != WP_TESTS_STYLESHEET) {
		switch_theme(WP_TESTS_TEMPLATE, WP_TESTS_STYLESHEET);
	} else {
		switch_theme(WP_TESTS_STYLESHEET, WP_TESTS_STYLESHEET);
	}
}	
