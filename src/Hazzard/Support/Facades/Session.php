<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\SessionStore
*/
class Session extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'session'; }
}