<?php namespace Hazzard\Support\Facades;

abstract class Facade {
	
    /**
     * The application instance being facaded.
     *
     * @var \Hazzard\Application
     */
    protected static $app;

    /**
     * The resolved object instances.
     *
     * @var array
     */
    protected static $resolvedInstance;

    /**
     * Get the registered name of the component.
     *
     * @return string
     * @throws \RuntimeException
     */
	protected static function getFacadeAccessor()
	{
        throw new \RuntimeException('Facade does not implement getFacadeAccessor method.');
	}

    /**
     * Resolve the facade root instance from the container.
     *
     * @param  string  $name
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) return $name;

        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }

        return static::$resolvedInstance[$name] = static::$app[$name];
    }

    /**
     * Set the application instance.
     *
     * @param  \Hazzard\Application  $app
     * @return void
     */
    public static function setFacadeApplication($app)
    {
        static::$app = $app;
    }

    /**
     * Get the application instance.
     *
     * @return  \Hazzard\Application  $app
     */
    public static function getFacadeApplication()
    {
        return static::$app;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param  string  $method
     * @param  array   $args
     * @return mixed
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::resolveFacadeInstance(static::getFacadeAccessor());

        return call_user_func_array(array($instance, $method), $args);
    }
}