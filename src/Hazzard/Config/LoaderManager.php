<?php namespace Hazzard\Config;

use Hazzard\Database\Connection;

class LoaderManager implements LoaderInterface {

	/**
	 * The file loader implementation.
	 *
	 * @var \Hazzard\Config\LoaderInterface
	 */
	protected $fileLoader;

	/**
	 * The database loader implementation.
	 *
	 * @var \Hazzard\Config\LoaderInterface
	 */
	protected $dbLoader;

	/**
	 * Create a new loader instance.
	 * 
	 * @return void
	 */
	function __construct($path)
	{
		$this->fileLoader = new FileLoader($path);
	}

	/**
     * Load the configuration group for the key.
     *
     * @param	string 	$group
     * @return 	array
     */
	public function load($group)
	{
		$items = $this->fileLoader->load($group);

		if (isset($this->dbLoader)) {
			$items = array_merge($items, $this->dbLoader->load($group));
		}

		return $items;
	}

	/**
	 * Set a given configuration value using the loader implementation.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function set($key, $value)
	{
		// We update only the configuration value from database
		if ($this->dbLoader) $this->dbLoader->set($key, $value);
	}

	/**
	 * Set the database connection instance.
	 *
	 * @var \Hazzard\Database\Connection
	 */
	public function setConnection(Connection $db)
	{
		$this->dbLoader = new DatabaseLoader($db);
	}

	/**
	 * Set the database table for the database loader.
	 *
	 * @param string
	 * @return void
	 */
	public function setTable($table)
	{
		$this->dbLoader->setTable($table);
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return \Hazzard\Database\Connection|null
	 */
	public function getDBLoader()
	{
		return $this->dbLoader;
	}
}