<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\User\Fields
*/
class UserFields extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'user.fields'; }
}