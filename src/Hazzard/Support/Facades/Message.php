<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Messages\Message
*/
class Message extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'user.message'; }
}