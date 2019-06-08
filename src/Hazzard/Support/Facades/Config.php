<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\ConfigManager
*/
class Config extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'config'; }
}