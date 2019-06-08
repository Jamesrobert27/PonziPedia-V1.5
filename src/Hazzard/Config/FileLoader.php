<?php namespace Hazzard\Config;

class FileLoader implements LoaderInterface {

	/**
	 *  The path to the config files.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * Create a new fileloader instance.
	 * 
	 * @return void
	 */
	function __construct($path)
	{
		$this->path = $path;
	}

	/**
     * Load the configuration group for the key.
     *
     * @param	string 	$group
     * @return 	array
     */
	public function load($group)
	{
		$items = array();

		foreach (array('/dev', '') as $dir) {
			$file = $this->path."{$dir}/{$group}.php";

			if (file_exists($file)) {
				return (array) include $file;
			}
		}

		return $items;
	}
}