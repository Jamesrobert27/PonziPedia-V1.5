<?php namespace Hazzard\Exception;

use Whoops\Run;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

class Handler {

	/**
	 * Indicates if the application is in debug mode.
	 *
	 * @var bool
	 */
	protected $debug;

	/**
	 * Create a new error handler instance.
	 *
	 * @param  bool  $debug
	 * @return void
	 */
	public function __construct($debug = true)
	{
		$this->debug = $debug;
	}

	/**
	 * Register the exception / error handlers.
	 *
	 * @return void
	 */
	public function register()
	{
		if ($this->debug) {
			if ($this->isAjaxRequest()) {
				$handler = new JsonResponseHandler;
				$handler->onlyForAjaxRequests(true);
			} else {
				$handler = new PrettyPageHandler;
			}
		} else {
			$handler = new PlainDisplayer;
		}

		$run = new Run;
		
		$run->pushHandler($handler);

		$run->register();
	} 

	/**
     * Check if is an AJAX request.
     *
     * @return bool
     */
    protected function isAjaxRequest()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

	/**
	 * Set the debug level for the handler.
	 *
	 * @param  bool  $debug
	 * @return void
	 */
	public function setDebug($debug)
	{
		$this->debug = $debug;
	}
}