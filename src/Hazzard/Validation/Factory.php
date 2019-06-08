<?php namespace Hazzard\Validation;

use Closure;
use Illuminate\Container\Container;
use Hazzard\Translation\Translator;

class Factory {

	/**
	 * The translator implementation.
	 *
	 * @var \Hazzard\Translation\Translator
	 */
	protected $translator;

	/**
	 * The databae presence verifier implementation.
	 *
	 * @var \Hazzard\Validation\DatabasePresenceVerifier
	 */
	protected $verifier;

	/**
	 * The IoC container instance.
	 *
	 * @var \Illuminate\Container\Container
	 */
	protected $container;

	/**
	 * All of the custom validator extensions.
	 *
	 * @var array
	 */
	protected $extensions = array();

	/**
	 * All of the custom implicit validator extensions.
	 *
	 * @var array
	 */
	protected $implicitExtensions = array();

	/**
	 * All of the custom validator message replacers.
	 *
	 * @var array
	 */
	protected $replacers = array();

	/**
	 * All of the fallback messages for custom rules.
	 *
	 * @var array
	 */
	protected $fallbackMessages = array();

	/**
	 * The Validator resolver instance.
	 *
	 * @var Closure
	 */
	protected $resolver;

	/**
	 * Create a new Validator factory instance.
	 *
	 * @param  \Hazzard\Translation\Translator  $translator
	 * @param  \Illuminate\Container\Container  $container
	 * @return void
	 */
	public function __construct(Translator $translator, Container $container = null)
	{
		$this->container = $container;
		$this->translator = $translator;
	}

	/**
	 * Create a new Validator instance.
	 *
	 * @param  array  $data
	 * @param  array  $rules
	 * @param  array  $messages
	 * @return \Hazzard\Validation\Validator
	 */
	public function make(array $data, array $rules, array $messages = array(), array $customAttributes = array())
	{
		$validator = $this->resolve($data, $rules, $messages, $customAttributes);

		if (!is_null($this->verifier)) {
			$validator->setPresenceVerifier($this->verifier);
		}

		if (!is_null($this->container)) {
			$validator->setContainer($this->container);
		}

		$this->addExtensions($validator);

		return $validator;
	}

	/**
	 * Add the extensions to a validator instance.
	 *
	 * @param  \Hazzard\Validation\Validator  $validator
	 * @return void
	 */
	protected function addExtensions($validator)
	{
		$validator->addExtensions($this->extensions);

		$implicit = $this->implicitExtensions;

		$validator->addImplicitExtensions($implicit);

		$validator->addReplacers($this->replacers);

		$validator->setFallbackMessages($this->fallbackMessages);
	}

	/**
	 * Resolve a new Validator instance.
	 *
	 * @param  array  $data
	 * @param  array  $rules
	 * @param  array  $messages
	 * @return \Hazzard\Validation\Validator
	 */
	protected function resolve($data, $rules, $messages, $customAttributes)
	{
		if (is_null($this->resolver)) {
			return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
		} else {
			return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
		}
	}

	/**
	 * Register a custom validator extension.
	 *
	 * @param  string  $rule
	 * @param  \Closure|string  $extension
	 * @param  string  $message
	 * @return void
	 */
	public function extend($rule, $extension, $message = null)
	{
		$this->extensions[$rule] = $extension;

		if ($message) $this->fallbackMessages[snake_case($rule)] = $message;
	}

	/**
	 * Register a custom implicit validator extension.
	 *
	 * @param  string   $rule
	 * @param  \Closure|string  $extension
	 * @param  string  $message
	 * @return void
	 */
	public function extendImplicit($rule, $extension, $message = null)
	{
		$this->implicitExtensions[$rule] = $extension;

		if ($message) $this->fallbackMessages[snake_case($rule)] = $message;
	}

	/**
	 * Register a custom implicit validator message replacer.
	 *
	 * @param  string   $rule
	 * @param  \Closure|string  $replacer
	 * @return void
	 */
	public function replacer($rule, $replacer)
	{
		$this->replacers[$rule] = $replacer;
	}

	/**
	 * Set the Validator instance resolver.
	 *
	 * @param  Closure  $resolver
	 * @return void
	 */
	public function resolver(Closure $resolver)
	{
		$this->resolver = $resolver;
	}

	/**
	 * Get the Translator implementation.
	 *
	 * @return \Hazzard\Translation\Translator
	 */
	public function getTranslator()
	{
		return $this->translator;
	}

	/**
	 * Get the database presence verifier implementation.
	 *
	 * @return \Hazzard\Validation\DatabasePresenceVerifier
	 */
	public function getPresenceVerifier()
	{
		return $this->verifier;
	}

	/**
	 * Set the database presence verifier implementation.
	 *
	 * @param  \Hazzard\Validation\DatabasePresenceVerifier  $presenceVerifier
	 * @return void
	 */
	public function setPresenceVerifier(DatabasePresenceVerifier $presenceVerifier)
	{
		$this->verifier = $presenceVerifier;
	}
}