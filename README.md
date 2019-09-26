# REST Pattern
Rest API Pattern class for easy create rest-api based WordPress.

[![Travis CI](https://travis-ci.org/hametuha/rest-pattern.svg?branch=master)](https://travis-ci.org/hametuha/rest-pattern)

## Installation

```
composer require haemtuha/rest-pattern
```

Then include `autoload.php` in your themes or plugins.

## Implementation

This library is abstarct class for REST API.
Inherit this class like below:

```php
<?php
namespace Vendor\Library\RestApi;

use Hametuha\RestPattern\RestApiBase;

class UsersApi extends RestApiBase {

	protected $namespace = 'vendor';

	protectd $route = 'user/(?P<user_id>\d+)';

	protected function get_args( $request ) {
		return [
			'user_id' => [
				'required' => true,
				'validate_callback' => function( $var ) {
					return $var && get_userdata( $var );
				}
			],
		];
	}
	
	protected function handle_get( $request ) {
		return [
			'success' => true,
			'user' => get_userdata( $request->get_param( 'user_id' ) ),
		];
	}

}
```

`handle_***` method will handel your request. They should return JSON convertible object(array, object), `WP_REST_Restponse`, or `WP_Error`. Alternatively, you can throw `Exception` in request handler and then will be automatically converted to `WP_Error`.

## License

GPL 3.0 or later.