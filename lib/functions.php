<?php

function wptest_install_plugins($plugins) {
	foreach($plugins as $plugin) {
		activate_plugin($plugin);
	}
}


function wp_test_load_plugin_tests() {
	$plugins = wp_get_active_and_valid_plugins();
	if(is_multisite()) {
		$plugins = array_merge($plugins, wp_get_active_network_plugins());
	}
	foreach($plugins as $plugin) {
		$dirname = dirname($plugin);
		if($dirname != WP_PLUGIN_DIR) {
			wp_test_load_tests($dirname);
		}
	}	
}

/**
 * Load theme tests.
 **/
function wptest_load_theme_tests() {
	// unfinished, obviously.
}

/**
 * Load scripts that should contain test cases from a directory inside of $dir
 * @todo support an array of possible test directories
 *
 * @param string $dir
 * @param string $test_dir
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
