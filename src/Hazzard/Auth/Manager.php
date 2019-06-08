<?php namespace Hazzard\Auth;

use Hazzard\Mail\Mailer;
use Hazzard\User\Fields;
use Hazzard\Cookie\CookieJar;
use Hazzard\Events\Dispatcher;
use Hazzard\Validation\Factory;
use Hazzard\Support\MessageBag;
use Hazzard\Translation\Translator;
use Hazzard\Session\Store as SessionStore;

class Manager {
	
	/**
	 * The user provider instance.
	 *
	 * @var \Hazzard\Auth\UserProvider
	 */
	protected $provider;

	/**
	 * The session store instance.
	 *
	 * @var \Hazzard\Session\Store
	 */
	protected $session;

	/**
	 * The cookie instance.
	 *
	 * @var \Hazzard\Cookie\CookieJar
	 */
	protected $cookie;

	/**
	 * The validator factory instance.
	 *
	 * @var \Hazzard\Validation\Factory
	 */
	protected $validator;

	/**
	 * The translator instance.
	 *
	 * @var \Hazzard\Translation\Translator
	 */
	protected $translator;

	/**
	 * The mailer instance.
	 *
	 * @var \Hazzard\Mail\Mailer
	 */
	protected $mailer;

	/**
	 * The userfields instance.
	 *
	 * @var \Hazzard\User\Fields
	 */
	protected $userfields;

	/**
	 * The event dispatcher instance.
	 *
	 * @var \Hazzard\Events\Dispatcher
	 */
	protected $events;

	/**
	 * The auth errors.
	 *
	 * @var \Hazzard\Support\MessageBag
	 */
	protected $errors;

	/**
	 * The auth config items.
	 *
	 * @var array
	 */
	protected $config;

	public function __construct()
	{
		$this->errors = new MessageBag;
	}

	/**
	 * Set the user provider instance.
	 *
	 * @param  \Hazzard\Auth\UserProvider
	 * @return self
	 */
	public function setProvider(UserProvider $provider)
	{
		$this->provider = $provider;

		return $this;
	}

	/**
	 * Get the user provider instance.
	 *
	 * @return \Hazzard\Auth\UserProvider
	 */
	public function getProvider()
	{
		return $this->provider;
	}

	/**
	 * Set the session instance.
	 *
	 * @param  \Hazzard\Session\Store
	 * @return self
	 */
	public function setSession(SessionStore $session)
	{
		$this->session = $session;

		return $this;
	}

	/**
	 * Get the session instance.
	 *
	 * @return \Hazzard\Session\Store
	 */
	public function getSession()
	{
		return $this->session;
	}

	/**
	 * Set the cookie instance.
	 *
	 * @param  \Hazzard\Events\Dispatcher
	 * @return self
	 */
	public function setCookie(CookieJar $cookie)
	{
		$this->cookie = $cookie;

		return $this;
	}

	/**
	 * Set the validator factory instance.
	 *
	 * @param  \Hazzard\Validation\Factory
	 * @return self
	 */
	public function setValidator(Factory $validator)
	{
		$this->validator = $validator;

		return $this;
	}

	/**
	 * Get the validator factory instance.
	 *
	 * @return  \Hazzard\Validation\Factory
	 */
	public function getValidator()
	{
		return $this->validator;
	}

	/**
	 * Set the translator instance.
	 *
	 * @param  \Hazzard\Translation\Translator
	 * @return self
	 */
	public function setTranslator(Translator $translator)
	{
		$this->translator = $translator;

		return $this;
	}

	/**
	 * Get the translator instance.
	 *
	 * @return \Hazzard\Translation\Translator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Set the mailer instance.
	 *
	 * @param  \Hazzard\Mail\Mailer
	 * @return self
	 */
	public function setMailer(Mailer $mailer)
	{
		$this->mailer = $mailer;

		return $this;
	}

	/**
	 * Set the user fields instance.
	 *
	 * @param  \Hazzard\User\Fields
	 * @return self
	 */
	public function setUserFields(Fields $fields)
	{
		$this->userfields = $fields;

		return $this;
	}

	/**
	 * Set the event dispatcher instance.
	 *
	 * @param  \Hazzard\Events\Dispatcher
	 * @return self
	 */
	public function setDispatcher(Dispatcher $events)
	{
		$this->events = $events;

		return $this;
	}

	public function setConfig(array $config)
	{
		$this->config = $config;
	}

	/**
	 * Determine if auth passes.
	 *
	 * @return bool
	 */
	public function passes()
	{
		return count($this->errors->all()) === 0;
	}

	/**
	 * Determine if the auth fails.
	 *
	 * @return bool
	 */
	public function fails()
	{
		return !$this->passes();
	}

	/**
	 * Get the auth errors.
	 *
	 * @return \Hazzard\Support\MessageBag
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * Dynamically retrieve properties on the class.
	 *
	 * @param  string  $property
	 * @return mixed
	 */
	public function __get($property)
	{
		return $this->$property;
	}
}