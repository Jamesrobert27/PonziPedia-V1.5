<?php namespace Hazzard\Auth;

use Hazzard\Support\ServiceProvider;
use Hazzard\Auth\Manager;

class RegisterServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('auth.register', function($app) {
			$provider = with(new UserProvider)->setHasher($app['hash'])->setUsermeta($app['user.meta']);

			$config = $app['config']['auth'];

			$register = new Register($provider, $app['validator'], $app['translator'], $config);
			
			$register->setDispatcher($app['events']);
			
			$register->setUserFields($app['user.fields']);

			$register->setMailer($app['mailer']);

			return $register; ;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth.register');
	}
}