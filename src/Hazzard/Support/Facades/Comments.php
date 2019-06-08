<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Comments\Comments
*/
class Comments extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'comments'; }
}