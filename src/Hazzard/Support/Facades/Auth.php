<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Authentication
*/
class Auth extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth'; }
}