## About

This is a fork of Nikolay Bachiyski's [alternative WordPress testing framework](https://github.com/nb/wordpress-tests). The fork makes testing plugins easier.

## Rationale

WordPress already has an automated [testing suite](http://unit-tests.trac.wordpress.org/). What you see here is an alternative testing framework, with the following goals:

* faster
* runs every test case in a clean WordPress install
* uses the default PHPUnit runner, instead of custom one
* doesn't encourage or support the usage of shared/prebuilt fixtures
* 

It uses SQL transactions to clean up automatically after each test.

## Installation

0. Install PHPUnit http://phpunit.de
1. Clone the project.
2. Copy `unittests-config-sample.php` to `unittests-config.php`.
3. Edit the config. USE A NEW DATABASE, BECAUSE ALL THE DATA INSIDE WILL BE DELETED.
4. `$ phpunit TestAll # test plugins and all tests`
5. `$ phpunit TestPlugins # test just plugins activated in unittests-config.php`
6. `$ phpunit test_test.php`

## Writing Tests for Plugins

Plugin tests should be stored in a wp-tests directory inside the plugin's root directory. Each file should be prepended with "test_" so that the test suite can automatically discover each test case.

Do not `include` or `require` the tests within your plugin.
