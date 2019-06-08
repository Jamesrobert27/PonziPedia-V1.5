<?php namespace Hazzard\Foundation;

use Illuminate\Container\Container;
use Hazzard\Events\EventServiceProvider;
use Hazzard\Exception\ExceptionServiceProvider;

class Application extends Container {

	/**
	 * All of the registered service providers.
	 *
	 * @var array
	 */
	protected $serviceProviders = array();

	/**
	 * The names of the loaded service providers.
	 *
	 * @var array
	 */
	protected $loadedProviders = array();

	/**
	 * The deferred services and their providers.
	 *
	 * @var array
	 */
	protected $deferredServices = array();

	/**
	 * Create a new application instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->registerBaseServiceProviders();
	}

	/**
	 * Register all of the base service providers.
	 *
	 * @return void
	 */
	protected function registerBaseServiceProviders()
	{
		foreach (array('Event', 'Exception') as $name) {
			$this->{"register{$name}Provider"}();
		}
	}

	/**
	 * Register the event service provider.
	 *
	 * @return void
	 */
	protected function registerEventProvider()
	{
		$this->register(new EventServiceProvider($this));
	}

	/**
	 * Register the exception service provider.
	 *
	 * @return void
	 */
	protected function registerExceptionProvider()
	{
		$this->register(new ExceptionServiceProvider($this));
	}

	/**
	 * Start the exception handling for the request.
	 *
	 * @return void
	 */
	public function startExceptionHandling()
	{
		$this['exception']->setDebug($this['config']['app.debug']);

		$this['exception']->register();
	}

	/**
	 * Bind the installation paths to the application.
	 *
	 * @param  string  $paths
	 * @return string
	 */
	public function bindInstallPaths(array $paths)
	{
		$this->instance('path', realpath($paths['app']));

		foreach ($paths as $key => $value) {
			$this->instance("path.{$key}", realpath($value));
		}
	}

	/**
	 * Register a service provider with the application.
	 *
	 * @param  \Hazzard\Support\ServiceProvider|string  $provider
	 * @param  array  $options
	 * @param  bool  $force
	 * @return \Hazzard\Support\ServiceProvider
	 */
	public function register($provider, $options = array(), $force = false)
	{
		if ($registered = $this->getRegistered($provider) && !$force)
			return $registered;

		if (is_string($provider)) {
			$provider = $this->resolveProviderClass($provider);
		}

		$provider->register();

		foreach ($options as $key => $value) {
			$this[$key] = $value;
		}

		$this->markAsRegistered($provider);

		return $provider;
	}

	/**
	 * Get the registered service provider instnace if it exists.
	 *
	 * @param  \Hazzard\Support\ServiceProvider|string  $provider
	 * @return \Hazzard\Support\ServiceProvider|null
	 */
	public function getRegistered($provider)
	{
		$name = is_string($provider) ? $provider : get_class($provider);

		if (array_key_exists($name, $this->loadedProviders)) {
			return array_first($this->serviceProviders, function($key, $value) use ($name) {
				return get_class($value) == $name;
			});
		}
	}

	/**
	 * Resolve a service provider instance from the class name.
	 *
	 * @param  string  $provider
	 * @return \Hazzard\Support\ServiceProvider
	 */
	public function resolveProviderClass($provider)
	{
		return new $provider($this);
	}

	/**
	 * Mark the given provider as registered.
	 *
	 * @param  \Hazzard\Support\ServiceProvider
	 * @return void
	 */
	protected function markAsRegistered($provider)
	{
		$class = get_class($provider);

		$this->serviceProviders[] = $provider;

		$this->loadedProviders[$class] = true;
	}

	/**
	 * Load and boot all of the remaining deferred providers.
	 *
	 * @return void
	 */
	public function loadDeferredProviders()
	{
		foreach ($this->deferredServices as $service => $provider) {
			$this->loadDeferredProvider($service);
		}

		$this->deferredServices = array();
	}

	/**
	 * Load the provider for a deferred service.
	 *
	 * @param  string  $service
	 * @return void
	 */
	protected function loadDeferredProvider($service)
	{
		$provider = $this->deferredServices[$service];

		if (!isset($this->loadedProviders[$provider])) {
			$this->registerDeferredProvider($provider, $service);
		}
	}

	/**
	 * Register a deffered provider and service.
	 *
	 * @param  string  $provider
	 * @param  string  $service
	 * @return void
	 */
	public function registerDeferredProvider($provider, $service = null)
	{
		if ($service) unset($this->deferredServices[$service]);

		$this->register($instance = new $provider($this));
	}

	/**
	 * Resolve the given type from the container.
	 *
	 * (Overriding \Illuminate\Container\Container::make)
	 *
	 * @param  string  $abstract
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function make($abstract, $parameters = array())
	{
		$abstract = $this->getAlias($abstract);

		if (isset($this->deferredServices[$abstract])) {
			$this->loadDeferredProvider($abstract);
		}

		return parent::make($abstract, $parameters);
	}

	/**
	 * Set the application's deferred services.
	 *
	 * @param  array  $services
	 * @return void
	 */
	public function setDeferredServices(array $services)
	{
		$this->deferredServices = $services;
	}

	/**
	 * Get the service provider repository instance.
	 *
	 * @return \Hazzard\Foundation\ProviderRepository
	 */
	public function getProviderRepository()
	{
		$manifest = $this['config']['app.manifest'];

		return new ProviderRepository($manifest);
	}

	/**
	 * Get the application URL.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function url($path = '')
	{
		$url = $this['config']['app.url'];

		if (!empty($url) && $url[strlen($url)-1] == '/') {
			$url = substr($url, 0, strlen($url)-1);
		}

		return $url.($path ? '/'.$path : $path);
	}
}