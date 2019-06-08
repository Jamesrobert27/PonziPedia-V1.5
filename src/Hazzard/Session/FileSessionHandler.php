<?php namespace Hazzard\Session;

class FileSessionHandler {
	
	/**
	 * The path where sessions should be stored.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The session lifetime.
	 *
	 * @var int
	 */
	protected $lifetime;

	/**
	 * Create a new instance.
	 * 
	 * @param  string 	$path
	 * @param  int 		$lifetime
	 * @return void
	 */
	function __construct($path, $lifetime)
	{
		$this->path = $path;

		$this->lifetime = $lifetime;

		session_set_save_handler(
			array($this, 'open'),
		    array($this, 'close'),
		    array($this, 'read'),
		    array($this, 'write'),
		    array($this, 'destroy'),
		    array($this, 'gc')
		);
	}

	/**
	 * File open handler.
	 * 
	 * @return bool
	 */
	public function open()
	{
		return true;
	}

	/**
	 * File close handler.
	 * 
	 * @return bool
	 */
	public function close()
	{
		return true;
	}

	/**
	 * File read handler.
	 * 
	 * @param  int  $id
	 * @return string
	 */
	public function read($id)
	{
		if (file_exists($this->path.'/'.$id)) {
			return file_get_contents($this->path.'/'.$id);
		}

		return '';
	}

	/**
	 * File write handler.
	 * 
	 * @param  int 		$id
	 * @param  string 	$data
	 * @return string
	 */
	public function write($id, $data)
	{
		file_put_contents($this->path.'/'.$id, $data);
	}

	/**
	 * File destroy handler.
	 * 
	 * @param  int  $id
	 * @return string
	 */
	public function destroy($id)
	{
		if (file_exists($this->path.'/'.$id)) {
			unlink($this->path.'/'.$id);
		}
	}

	/**
	 * File gc handler.
	 * 
	 * @param  int  $lifetime
	 * @return string
	 */
	public function gc($lifetime)
	{
		if (empty($this->lifetime)) {
			$this->lifetime = $lifetime;
		}

		$time = time() - $this->lifetime;

		foreach (scandir($this->path) as $file) {
			$file = $this->path.'/'.$file;
			$mod  = filemtime($file);

			if (file_exists($file) && $mod < $time) {
				unlink($this->path.'/'.$file);
			}
		}
	}
}