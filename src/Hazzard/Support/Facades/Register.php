<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Auth\Register
*/
class Register extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth.register'; }
}