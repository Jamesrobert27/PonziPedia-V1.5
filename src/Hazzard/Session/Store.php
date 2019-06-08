<?php namespace Hazzard\Session;

class Store implements \ArrayAccess {
	
	/**
	 * Create a new session store instance.
	 *
	 * @param  string  $name
	 * @return void
	 */
	public function __construct($name)
	{
		$this->setName($name);
	}

	/**
	 * Start the session.
	 * 
	 * @return \Hazzard\Session\Store
	 */
	public function start()
	{
		if (!$this->getId()) {
			session_start();
		}

		if (!$this->has('_token')) $this->regenerateToken();

		return $this;
	}

	/**
	 * Get the current session id.
	 * 
	 * @return string
	 */
	public function getId()
	{
		return session_id();
	}

	/**
	 * Set the current session id.
	 *
	 * @param  string  $id
	 * @return void
	 */
	public function setId($id)
	{
		return session_id($id);
	}

	/**
	 * Get the current session name.
	 * 
	 * @return string
	 */
	public function getName()
	{
		return session_name();
	}

	/**
	 * Set the current session name.
	 *
	 * @param  string  $name
	 * @return string
	 */
	public function setName($name)
	{
		return session_name($name);
	}

	/**
	 * Set a key / value pair or array of key / value pairs in the session.
	 *
	 * @param  string|array  $key
	 * @param  mixed|null  	 $value
	 * @return void
	 */
	public function set($key, $value = null)
	{
		if (!is_array($key)) $key = array($key => $value);

		foreach ($key as $arrayKey => $arrayValue) {
			array_set($_SESSION, $arrayKey, $arrayValue);
		}
	}

	/**
	 * Push a value onto an array session value.
	 * 
	 * @param  string  $key
	 * @param  string  $value
	 * @return void
	 */
	public function push($key, $value)
	{
		$array = $this->get($key, array());

		$array[] = $value;

		$this->set($key, $array);
	}

	/**
	 * Flash a key / value pair to the session.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function flash($key, $value)
	{
		$this->set($key, $value);

		$this->push('flash', $key);
	}


	/**
	 * Delete all the flashed data.
	 *
	 * @return void
	 */
	public function deleteFlash()
	{
		foreach ($this->get('flash', array()) as $key) {
			$this->delete($key);	
		}

		$this->set('flash', array());
	}

	/**
	 * Retrieve an item from the session.
	 * 
	 * @param  string  $name
	 * @param  mixed   $default
	 * @return mixed
	 */
	public function get($name, $default = null)
	{
		return array_get($_SESSION, $name, $default);
	}

	/**
	 * Retrieve all items from the session.
	 * 
	 * @return array
	 */
	public function all()
	{
		return $_SESSION;
	}

	/**
	 * Determine if an item exists in the session.
	 * 
	 * @param  string  $name
	 * @return mixed
	 */
	public function has($name)
	{
		return $this->get($name);
	}

	/**
	 * Remove an item from the session.
	 * 
	 * @param  string  $key
	 * @return void
	 */
	public function delete($key)
	{
		array_forget($_SESSION, $key);
	}

	/**
	 * Destroy all data registered to a session.
	 * 
	 * @return bool
	 */
	public function destroy()
	{
		if ($this->getId()) return session_destroy();
	}

	/**
	 * Remove all items from the session.
	 * 
	 * @return bool
	 */
	public function flush()
	{
		return session_unset();
	}

	/**
	 * Get CSRF token value.
	 * 
	 * @return void
	 */
	public function token()
	{
		return $this->get('_token');
	}

	/**
	 * Regenerate the CSRF token value.
	 *
	 * @return void
	 */
	public function regenerateToken()
	{
		$this->set('_token', md5(mt_rand()));
	}

	/**
	 * Determine if the given configuration option exists.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function offsetExists($key)
	{
		return $this->has($key);
	}

	/**
	 * Get a configuration option.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function offsetGet($key)
	{
		return $this->get($key);
	}

	/**
	 * Set a configuration option.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function offsetSet($key, $value)
	{
		$this->set($key, $value);
	}

	/**
	 * Unset a configuration option.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function offsetUnset($key)
	{
		$this->delete($key);
	}
}