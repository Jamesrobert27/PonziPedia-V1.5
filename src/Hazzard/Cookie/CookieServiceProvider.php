<?php namespace Hazzard\Cookie;

use Hazzard\Support\ServiceProvider;

class CookieServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('cookie', function($app) {
			
			$config = $app['config']['session'];

			return with(new CookieJar)->setDefaultPathAndDomain($config['path'], $config['domain']);
		});
	}
}