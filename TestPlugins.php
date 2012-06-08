<?php
require_once 'PHPUnit/Autoload.php';

wp_test_load_plugin_tests();

class TestPlugins {
    public static function suite() {
        $suite = new PHPUnit_Framework_TestSuite();
		
		foreach( get_declared_classes() as $class ) {
			if ( is_subclass_of( $class, 'WP_UnitTestCase' ) ) {
				$suite->addTestSuite( $class );
			}
		}
        return $suite;
    }
}
