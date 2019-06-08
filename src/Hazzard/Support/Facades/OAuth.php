<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Auth\OAuth
*/
class OAuth extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth.oauth'; }
}