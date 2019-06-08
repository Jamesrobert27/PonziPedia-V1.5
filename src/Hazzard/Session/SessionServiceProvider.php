<?php namespace Hazzard\Session;

use Hazzard\Support\ServiceProvider;

class SessionServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$me = $this;

		$this->app->bindShared('session', function($app) use($me) {
			
			$config = $app['config']['session'];

			$me->registerSessionDriver($config);

			return with(new Store($config['cookie']))->start();
		});
	}

	/**
	 * Register session driver.
	 *
	 * @param  array  $config
	 * @return void
	 */
	public function registerSessionDriver(array $config)
	{
		$lifetime = (int) $config['lifetime'] * 60;

		switch ($config['driver']) {
			case 'database':
				new DatabaseSessionHandler($this->app['db'], $config['table'], $lifetime);
			break;

			case 'file':
				new FileSessionHandler($config['files'], $lifetime);
			break;
		}
	}
}