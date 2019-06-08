<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Hashing\Hasher
*/
class Hash extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'hash'; }
}