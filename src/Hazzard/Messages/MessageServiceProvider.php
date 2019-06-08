<?php namespace Hazzard\Messages;

use Hazzard\Support\ServiceProvider;
use Hazzard\Auth\UserProvider;

class MessageServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('user.message', function($app) {
			return new Message($app['db'], new UserProvider, $app['validator']);
		});

		$this->app->bindShared('user.contact', function($app) {
			return new Contact($app['db'], new UserProvider);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('user.message', 'user.contact');
	}
}