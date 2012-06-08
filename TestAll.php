<?php
require_once 'PHPUnit/Autoload.php';

wp_test_load_plugin_tests();

$tests_dir = dirname( __FILE__ );
$old_cwd = getcwd();
chdir( $tests_dir );

for( $depth = 0; $depth <= 3; $depth++ ) {
	foreach( glob( str_repeat( 'tests[_-]*/', $depth ) . 'test_*.php' ) as $test_file ) {
		include_once $test_file;
	}	
}

class TestAll {
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

chdir( $old_cwd );
