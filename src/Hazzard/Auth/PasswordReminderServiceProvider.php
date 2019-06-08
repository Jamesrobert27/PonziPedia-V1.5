<?php namespace Hazzard\Auth;

use Hazzard\Support\ServiceProvider;

class PasswordReminderServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('auth.reminder', function($app) {
			$config = $app['config']['auth'];

			$provider = with(new UserProvider)->setHasher($app['hash']);

			$config = $app['config']['auth'];

			$reminder = new PasswordReminder($provider, $app['validator'], $app['translator'], $config);
			
			$reminder->setDispatcher($app['events']);

			$reminder->setMailer($app['mailer']);

			return $reminder;
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth.reminder');
	}
}