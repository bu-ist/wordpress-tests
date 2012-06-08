<?php

function wptest_install_plugins($plugins) {
	
	foreach($plugins as $plugin) {
		activate_plugin($plugin);
	}
}


function wptest_load_plugin_tests() {

}

function wptest_load_muplugin_tests() {

}

function wptest_load_theme_tests() {

}
