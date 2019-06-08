<?php namespace Hazzard\Events;

class Dispatcher {

	/**
	 * The registered event listeners.
	 *
	 * @var array
	 */
	protected $listeners = array();

	/**
	 * The wildcard listeners.
	 *
	 * @var array
	 */
	protected $wildcards = array();

	/**
	 * The sorted event listeners.
	 *
	 * @var array
	 */
	protected $sorted = array();

	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  string|array  $event
	 * @param  mixed   $listener
	 * @param  int     $priority
	 * @return void
	 */
	public function listen($events, $listener, $priority = 0)
	{
		foreach ((array) $events as $event) {
			if (str_contains($event, '*')) {
				return $this->setupWildcardListen($event, $listener);
			}

			$this->listeners[$event][$priority][] = $this->makeListener($listener);

			unset($this->sorted[$event]);
		}
	}

	/**
	 * Setup a wildcard listener callback.
	 *
	 * @param  string  $event
	 * @param  mixed   $listener
	 * @return void
	 */
	protected function setupWildcardListen($event, $listener)
	{
		$this->wildcards[$event][] = $this->makeListener($listener);
	}

	/**
	 * Determine if a given event has listeners.
	 *
	 * @param  string  $eventName
	 * @return bool
	 */
	public function hasListeners($eventName)
	{
		return isset($this->listeners[$eventName]);
	}

	/**
	 * Fire an event and call the listeners.
	 *
	 * @param  string  $event
	 * @param  mixed   $payload
	 * @return array|null
	 */
	public function fire($event, $payload = array())
	{
		$responses = array();

		if (!is_array($payload)) $payload = array($payload);

		foreach ($this->getListeners($event) as $listener) {
			$response = call_user_func_array($listener, $payload);

			if (!is_null($response)) {
				return $response;
			}

			if ($response === false) break;

			$responses[] = $response;
		}

		return $responses;
	}

	/**
	 * Get all of the listeners for a given event name.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	public function getListeners($eventName)
	{
		$wildcards = $this->getWildcardListeners($eventName);

		if (!isset($this->sorted[$eventName])) {
			$this->sortListeners($eventName);
		}

		return array_merge($this->sorted[$eventName], $wildcards);
	}

	/**
	 * Get the wildcard listeners for the event.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function getWildcardListeners($eventName)
	{
		$wildcards = array();

		foreach ($this->wildcards as $key => $listeners) {
			if (str_is($key, $eventName)) $wildcards = array_merge($wildcards, $listeners);
		}

		return $wildcards;
	}

	/**
	 * Sort the listeners for a given event by priority.
	 *
	 * @param  string  $eventName
	 * @return array
	 */
	protected function sortListeners($eventName)
	{
		$this->sorted[$eventName] = array();

		if (isset($this->listeners[$eventName])) {
			krsort($this->listeners[$eventName]);

			$this->sorted[$eventName] = call_user_func_array('array_merge', $this->listeners[$eventName]);
		}
	}

	/**
	 * Register an event listener with the dispatcher.
	 *
	 * @param  mixed  $listener
	 * @return mixed
	 */
	public function makeListener($listener)
	{
		if (is_string($listener)) {
			$listener = $this->createClassListener($listener);
		}

		return $listener;
	}

	/**
	 * Create a class based listener.
	 *
	 * @param  string  $listener
	 * @return \Closure
	 */
	public function createClassListener($listener)
	{
		return function() use ($listener) {
			$segments = explode('@', $listener);

			$method = count($segments) == 2 ? $segments[1] : 'handle';

			$callable = array($segments[0], $method);

			$data = func_get_args();

			return call_user_func_array($callable, $data);
		};
	}

	/**
	 * Remove a set of listeners from the dispatcher.
	 *
	 * @param  string  $event
	 * @return void
	 */
	public function forget($event)
	{
		unset($this->listeners[$event]);

		unset($this->sorted[$event]);
	}
}