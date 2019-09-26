<?php

namespace Hametuha\RestPattern;


use Hametuha\SingletonPattern\Singleton;

/**
 * Rest API base class.
 *
 * @package rest-pattern
 */
abstract class RestApiBase extends Singleton {
	
	protected $version = '1';
	
	protected $namespace = 'hametuha';
	
	protected $route     = '';
	
	/**
	 * Initialize this api
	 *
	 * @throws \Exception
	 */
	protected function init() {
		if ( ! $this->route ) {
			throw new \Exception( sprintf( __( '%s: REST API route is empty.', 'karma' ), get_called_class() ), 500 );
		}
		add_action( 'rest_api_init', [ $this, 'rest_api_init' ] );
	}
	
	/**
	 * Detect if
	 *
	 * @param mixed $var
	 * @param \WP_REST_Request $request
	 * @param $key
	 * @return bool
	 */
	public function is_not_empty( $var, \WP_REST_Request $request, $key ) {
		return ! empty( $var );
	}
	
	/**
	 * Check if this is numeric
	 *
	 * @param mixed $var
	 * @param \WP_REST_Request $request
	 * @param $key
	 * @return bool
	 */
	public function is_numeric( $var, \WP_REST_Request $request, $key ) {
		return is_numeric( $var );
	}
	
	/**
	 * Check if this is DATE(YYYY-MM-DD) format.
	 * @param mixed $var
	 * @param \WP_REST_Request $request
	 * @param $key
	 * @return bool
	 */
	public function is_date( $var, \WP_REST_Request $request, $key ) {
		return (bool) preg_match( '/^\d{4}-\d{2}-\d{2}$/u', $var );
	}
	
	/**
	 * Check if this is DATETIME (YYYY-MM-DD HH:ii:ss) format.
	 * @param mixed $var
	 * @param \WP_REST_Request $request
	 * @param $key
	 * @return bool
	 */
	public function is_datetime( $var, \WP_REST_Request $request, $key ) {
		return (bool) preg_match( '/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/u', $var );
	}
	
	/**
	 * Register rest route.
	 */
	public function rest_api_init() {
		$args = [];
		foreach ( [ 'GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS', 'HEAD' ] as $http_method ) {
			$method_name = 'handle_' . strtolower( $http_method );
			if ( method_exists( $this, $method_name ) ) {
				$arg = [
					'methods' => $http_method,
					'callback' => [ $this, 'callback' ],
					'args' => $this->get_args( $http_method ),
				];
				$permission_callback = method_exists( $this, 'permission_callback' ) ? [ $this, 'permission_callback' ] : null;
				if ( $permission_callback ) {
					$arg['permission_callback'] = $permission_callback;
				}
				$args[] = $arg;
			}
		}
		if ( $args ) {
			register_rest_route( "{$this->namespace}/v{$this->version}", $this->route, $args );
		}
	}
	
	/**
	 * Handle request.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_Error|\WP_REST_Response
	 */
	public function callback( \WP_REST_Request $request ) {
		$method_name = 'handle_' . strtolower( $request->get_method() );
		try {
			$result = $this->{$method_name}( $request );
			if ( is_wp_error( $result ) ) {
				return $result;
			} elseif( is_a( $result, 'WP_REST_Response' ) ) {
				return $result;
			} else {
				return new \WP_REST_Response( $result );
			}
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage(), [
				'response' => $e->getCode(),
			] );
		}
	}
	
	/**
	 * Should return arguments.
	 *
	 * @param string $http_method
	 * @return array
	 */
	abstract protected function get_args( $http_method );
}