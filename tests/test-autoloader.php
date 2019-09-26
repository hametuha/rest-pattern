<?php

use Hametuha\RestPatternTest\RestSample;


/**
 * Test autoloader.
 *
 * @package rest-pattern
 */
class AutoloaderTest extends WP_UnitTestCase {

	public function test_register() {
		$this->assertTrue( class_exists( 'Hametuha\RestPattern\RestApiBase' ) );
		$this->assertWPError( RestSample::get_instance()->handle_get( new WP_REST_Request()) );
	}
}
