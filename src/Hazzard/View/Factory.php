<?php namespace Hazzard\View;

class Factory {

	/**
	 * The path to the view files.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 *	Create new view factory instance.
	 * 
	 *  @param  string  $path
	 *	@return void
	 */
	function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Create a new view.
	 *
	 * @param	string 	$view
	 * @param	array 	$data
	 * @return	\Hazzard\View\View
	 */
	public function make($view, array $data = array())
	{
		$path = $this->path.'/'.$this->viewFile($view);

		return new View($view, $path, $data);
	}

	/**
	 * Check if the view file exists.
	 *
	 * @param	string 	$view
	 * @return	bool
	 */
	public function exists($view)
	{
		$path = $this->path.'/'.$this->viewFile($view);

		return file_exists($path);
	}

	/**
	 * Get the view file.
	 *
	 * @param	string 	$view
	 * @return	string
	 */
	protected function viewFile($view)
	{
		return str_replace('.', '/', $view).'.php';
	}
}