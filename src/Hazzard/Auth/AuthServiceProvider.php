<?php namespace Hazzard\Auth;

use Hazzard\Support\ServiceProvider;
use Hazzard\Auth\Manager;

class AuthServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('auth', function($app) {
			$provider = with(new UserProvider)->setHasher($app['hash'])->setUsermeta($app['user.meta']);
			
			$auth = new Auth($provider, $app['session'], $app['cookie'], $app['validator'], $app['translator'], $app['encrypter'], $app['config']['auth']);

			return with($auth)->setDispatcher($app['events']);
		});
	}
}