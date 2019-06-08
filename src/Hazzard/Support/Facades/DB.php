<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Database
*/
class DB extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'db'; }
}