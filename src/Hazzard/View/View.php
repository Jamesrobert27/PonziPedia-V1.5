<?php namespace Hazzard\View;

use Closure;

class View {

	/**
	 * The name of the view.
	 *
	 * @var string
	 */
	protected $view;

	/**
	 * The array of view data.
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * The path to the view file.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 *	Create new instance.
	 * 
	 *	@return void
	 */
	function __construct($view, $path, $data = array())
	{
		$this->view = $view;

		$this->data = $data;

		$this->path = $path;
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @param  \Closure  $callback
	 * @return string
	 */
	public function render(Closure $callback = null)
	{
		$response = isset($callback) ? $callback($this) : null;

		$contents = $this->renderContents();

		return $contents;
	}

	/**
	 * Add a piece of data to the view.
	 *
	 * @param  string|array  $key
	 * @param  mixed   $value
	 * @return \Hazzard\View\View
	 */
	public function with($key, $value = null)
	{
		if (is_array($key)) {
			$this->data = array_merge($this->data, $key);
		} else {
			$this->data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Get the contents of the view instance.
	 *
	 * @return string
	 */
	protected function renderContents()
	{
		if (!file_exists($this->path)) {
			throw new \InvalidArgumentException("Unable to load the view '".$this->view."'. File '".$this->path."' not found.", 1);
		}

		ob_start();

		extract($this->data);

		include $this->path;
		
		$contents = ob_get_contents();

		if (ob_get_contents()) {
			ob_end_clean();
		}

		return $contents;
	}

	/**
	 * Get the string contents of the view.
	 *
	 * @return string
	 */
	public function __toString()
	{
		try {
        	return $this->render();
	    } catch (\Exception $e) {
			return '';
	    }
	}
}

