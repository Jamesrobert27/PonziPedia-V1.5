<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\Translation\Translator
*/
class Lang extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'translator'; }
}