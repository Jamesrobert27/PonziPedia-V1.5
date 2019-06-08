<?php namespace Hazzard\Comments;

use Hazzard\Support\ServiceProvider;

class CommentsServiceProvider extends ServiceProvider {

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
		$this->app->bindShared('comments', function($app) {
			$comments = new Comments($app['config']['comments'], $app['auth'], $app['validator']);
			$comments->setDispatcher($app['events']);
			
			return $comments;
		});
		
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('comments');
	}
}