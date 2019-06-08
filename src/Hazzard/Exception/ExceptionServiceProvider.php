<?php namespace Hazzard\Exception;

use Hazzard\Support\ServiceProvider;

class ExceptionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['exception'] = $this->app->share(function($app) {
			return new Handler;
		});
	}
}