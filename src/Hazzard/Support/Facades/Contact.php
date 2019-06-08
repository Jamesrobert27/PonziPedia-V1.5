<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Messages\Contact
*/
class Contact extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'user.contact'; }
}