<?php namespace Hazzard\Translation;

use Hazzard\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('translator', function($app) {

			$path = $app['path'].'/lang';
			$locale = $app['config']['app.locale'];

			return new Translator($path, $locale, 'en');
		});
	}


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('translator');
	}
}