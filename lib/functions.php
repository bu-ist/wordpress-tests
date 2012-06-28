<?php

/**
 * Activate plugins.
 *
 * @param array $plugins Plugins to activate.
 **/
function wp_test_install_plugins($plugins, $network_wide = false) {
	foreach($plugins as $plugin) {
		$valid = activate_plugin($plugin, '', $network_wide);
		if(is_wp_error($valid)) {
			echo "Plugin $plugin could not be activated " . $valid->get_error_message() . "\n";
		}
	}
}

/**
 * Load plugin tests.
 *
 * @param array $test_dirs Directories to discover tests within
 * @param string $starts_with Filename prefix used for tests.
 **/
function wp_test_load_plugin_tests( $test_dirs = array('wp-tests'), $starts_with = 'test_') {
	$plugins = wp_get_active_and_valid_plugins();
	if(is_multisite()) {
		$plugins = array_merge($plugins, wp_get_active_network_plugins());
	}
	foreach($plugins as $plugin) {
		$dirname = dirname($plugin);
		if($dirname != WP_PLUGIN_DIR) {
			wp_test_load_tests($dirname, $test_dirs, $starts_with);
		}
	}	
}

/**
 * Load mu-plugin tests.
 *
 * @param array $test_dirs Directories to discover tests within
 * @param string $starts_with Filename prefix used for tests.
 **/
function wp_test_load_muplugin_tests( $test_dirs = array('wp-tests'), $starts_with = 'test_') {
	if( !defined('WPMU_PLUGIN_DIR') || !is_dir( WPMU_PLUGIN_DIR ) )  {
		return false;
	}
	
	$dh = opendir( WPMU_PLUGIN_DIR );

	if( !$dh ) {
		return false;
	}
	
	while( ( $plugin = readdir( $dh ) ) !== false ) {
		if(	is_dir( $plugin ) ) {
			wp_test_load_tests( $plugin, $test_dirs, $starts_with );
		}
	}
}

/**
 * Load theme tests.
 *
 * @param array $test_dirs Directories to discover tests within
 * @param string $starts_with Filename prefix used for tests.
 **/
function wp_test_load_theme_tests($test_dirs = array('wp-tests'), $starts_with = 'test_') {
	
	if( TEMPLATEPATH !== STYLESHEETPATH ) {
		wp_test_load_tests( STYLESHEETPATH, $test_dirs, $starts_with );
	}
	wp_test_load_tests( TEMPLATEPATH, $test_dirs, $starts_with );
}

/**
 * Load scripts that should contain test cases from a directory inside of $dir
 *
 * @param string $dir Directory to look for tests within.
 * @param array $test_dirs Relative directories to discover tests within
 * @param string $starts_with Filename prefix used for tests.
 **/
function wp_test_load_tests( $dir, $test_dirs = array('wp-tests'), $starts_with = 'test_') { 
	if(!is_dir($dir) || $dir[0] == '.') return false;
	
	foreach($test_dirs as $test_dir) {
		if($test_dir[0] == '.') continue;

		$tests_path = realpath(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $test_dir);		
		
		if($dh = opendir($tests_path)) {

			// read through the test directory looking for tests
			while (($file = readdir($dh)) !== false) {
				$path = realpath($tests_path . DIRECTORY_SEPARATOR . $file);

				$fileparts = pathinfo($file);

				// add .php files starting with 'test'
				if (is_file($path) && strpos($fileparts['basename'], $starts_with) === 0 && $fileparts['extension'] == 'php') {
					require_once $path;
				}
			}
			closedir($dh);
		}
	}
}
