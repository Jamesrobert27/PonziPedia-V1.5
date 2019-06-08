<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Auth\PasswordReminder
*/
class Password extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'auth.reminder'; }
}