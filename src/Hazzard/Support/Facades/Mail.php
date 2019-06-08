<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Mailer
*/
class Mail extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'mailer'; }
}