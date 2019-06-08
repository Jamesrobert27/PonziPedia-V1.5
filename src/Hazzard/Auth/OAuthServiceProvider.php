<?php namespace Hazzard\Auth;

use Hazzard\Support\ServiceProvider;
use Hazzard\Auth\Manager;

class OAuthServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('auth.oauth', function($app) {
			$provider = with(new UserProvider)->setHasher($app['hash'])->setUsermeta($app['user.meta']);

			$oauth = new OAuth($provider, $app['validator'], $app['translator'], $app['auth'], $app['config']['auth']);

			$oauth->setDispatcher($app['events']);

			$oauth->setUserFields($app['user.fields']);

			return $oauth;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth.oauth');
	}
}