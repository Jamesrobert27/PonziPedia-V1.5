<?php namespace Hazzard\Support\Facades;

/**
 * @see \Hazzard\Validation\Factory
 */
class Validator extends Facade {

	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'validator'; }

}
