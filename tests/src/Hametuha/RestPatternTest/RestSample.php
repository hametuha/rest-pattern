<?php

namespace Hametuha\RestPatternTest;


use Hametuha\RestPattern\RestApiBase;

/**
 * Simple customizer.
 *
 * @package theme-customizer
 */
class RestSample extends RestApiBase{
	
	protected $route = '/test';
	
	protected function get_args( $http_method ) {
		return [];
	}
	
	public function handle_get( \WP_REST_Request $request ) {
		return new \WP_Error( 'vanity_rest', 'This is a empty REST' );
	}
}
