<?php namespace Hazzard\Hashing;

use Hazzard\Support\ServiceProvider;

class HashServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('hash', function($app) {
			return new BcryptHasher;
		});

		// Use this if you don't have BCRYPT on your server.
		// $this->app->bindShared('hash', function($app) {
		// 	// "md5", "sha256", "haval160,4" etc.
		// 	return new BasicHasher('md5');
		// });
		
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('hash');
	}

}