<?php namespace Hazzard\Hashing;

class BasicHasher implements HasherInterface {

	/**
	 * Hasher algorithm.
	 *
	 * @var string
	 */
	protected $algo;

	/**
	 * Create a new hasher instance.
	 *
	 * @param  string  $algo
	 * @return voiod
	 */
	public function __construct($algo)
	{
		$this->algo = $algo;
	}

	/**
	 * Hash the given value.
	 *
	 * @param  string  $value
	 * @param  array   $options
	 * @return string
	 */
	public function make($value, array $options = array())
	{
		return hash($this->algo, $value);
	}

	/**
	 * Check the given plain value against a hash.
	 *
	 * @param  string  $value
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool
	 */
	public function check($value, $hashedValue, array $options = array())
	{
		return $this->make($value) == $hashedValue;
	}

	/**
	 * Check if the given hash has been hashed using the given options.
	 *
	 * @param  string  $hashedValue
	 * @param  array   $options
	 * @return bool
	 */
	public function needsRehash($hashedValue, array $options = array())
	{
		return true;
	}
	
}