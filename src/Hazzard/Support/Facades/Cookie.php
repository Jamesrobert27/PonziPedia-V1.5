<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\CookieJar
*/
class Cookie extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'cookie'; }
}