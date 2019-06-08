<?php namespace Hazzard\Validation;

use Hazzard\Support\Recaptcha;
use Hazzard\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider {

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
		$me = $this;

		$this->registerPresenceVerifier();

		$this->app->bindShared('validator', function($app) use ($me) {
			$validator = new Factory($app['translator'], $app);

			if (isset($app['validation.presence'])) {
				$validator->setPresenceVerifier($app['validation.presence']);
			}

			if ($app['config']['auth.captcha']) {
				$me->registerCaptcha($validator);
			}

			return $validator;
		});
	}

	/**
	 * Register the database presence verifier.
	 *
	 * @return void
	 */
	protected function registerPresenceVerifier()
	{
		$this->app->bindShared('validation.presence', function($app) {
			return new DatabasePresenceVerifier($app['db']);
		});
	}

	/**
	 * Add custom validation for captcha.
	 *
	 * @return void
	 */
	public function registerCaptcha($validator)
	{
		$config = $this->app['config']['services']['recaptcha'];

		$validator->extend('captcha', function($attrs, $response) use ($config) {
			$recaptcha = new Recaptcha($config['private_key'], $config['public_key']);

			return $recaptcha->verifyResponse($response, $_SERVER['REMOTE_ADDR']);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('validator', 'validation.presence');
	}
}