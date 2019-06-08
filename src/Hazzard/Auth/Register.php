<?php namespace Hazzard\Auth;

use Hazzard\Validation\Factory;
use Hazzard\Translation\Translator;

class Register extends Manager {

	/**
	 * Create a new register instance.
	 *
	 * @param  \Hazzard\Auth\UserProvider       $provider
	 * @param  \Hazzard\Validation\Factory      $validator
	 * @param  \Hazzard\Translation\Translator  $translator
	 * @return void
	 */
	public function __construct(UserProvider $provider, Factory $validator, Translator $translator, array $config)
	{
		$this->config = $config;
		$this->provider = $provider;
		$this->validator = $validator;
		$this->translator = $translator;

		parent::__construct();
	}

	/**
	 * Create a new user account.
	 *
	 * @param  array  $input
	 * @return bool
	 */
	public function signup(array $input)
	{
		$this->errors->clear();

		$data = array(
	    	'email' => $input['email'],
	    	'password' => $input['pass1'],
	    	'password_confirmation' => isset($input['pass2']) ? $input['pass2'] : ''
	    );

		$rules = array(
	    	'email' => 'required|email|max:100|unique:users',
	    	'password' => 'required|between:4,30'.(isset($input['pass2'])?'|confirmed':'')
	    );

		if ($this->config['require_username']) {
	    	$data['username'] = $input['username'];
	    	$rules['username'] = 'required|min:3|max:50|alpha_dash|unique:users';
	    }

	    foreach ($this->userfields->all('signup') as $key => $field) {
	    	if (!empty($field['validation'])) {
	    		$data[$key] = @$input[$key];
	    		$rules[$key] = $field['validation'];
	    	}
		}

		if ($this->config['captcha']) {
			$data['g-recaptcha-response'] = @$input['g-recaptcha-response'];
			$rules['g-recaptcha-response'] = 'required|captcha';
	    }

	    $validator = $this->validator->make($data, $rules);

		if ($validator->fails()) {
			$this->errors = $validator->errors();
			return false;
		}

		$user = array(
			'email'    => $input['email'],
			'password' => $input['pass1'],
			'role_id'  => $this->config['default_role_id'],
			'display_name' => $this->generateDisplayName($input)
		);
		
		if ($this->config['require_username']) {
			$user['username'] = $input['username'];
		}

		if (!$this->config['email_activation']) {
			$user['status'] = 1;
		}

		foreach ($this->userfields->all('signup') as $key => $field) {
			$user['usermeta'][$key] = escape($input[$key]);
		}

		$user = $this->provider->create($user);

		if (is_null($user)) {
			$this->errors->add('error', $this->translator->trans('errors.dbsave'));
			return false;
		}
 
		if ($this->config['email_activation']) {
			$reminder = str_random(32);

			$this->provider->setReminder($user->id, $reminder);

			$me = $this;
			
			$this->mailer->send('emails.activation', compact('reminder'), function($message) use($user, $me) {
			    $message->to($user->email);
			    $message->subject($me->translator->trans('emails.activation_subject'));
			});
		}

		if (isset($this->events)) {
			$this->events->fire('auth.signup', array($user));
		}

		return true;
	}

	/**
	 * Send the activation reminder.
	 *
	 * @param  string  $email
	 * @param  string  $captchaResponse
	 * @return bool
	 */
	public function reminder($email, $captchaResponse = null)
	{
		$this->errors->clear();

		$data  = array('reminder_email' => $email);
		$rules = array('reminder_email' => 'required|email|exists:users,email');

		if ($this->config['captcha']) {
			$data['g-recaptcha-response'] = $captchaResponse;
	    	$rules['g-recaptcha-response'] = 'required|captcha';
	    }

		$validator = $this->validator->make($data, $rules);

		if ($validator->fails()) {
			$this->errors = $validator->errors();
			return false;
		}
		
		$user = $this->provider->get(array('email' => $email));
		
		if ($user->isSuspended()) {
			$this->errors->add('error', $this->translator->trans('errors.suspended'));
			return false;
		} elseif ($user->isActive()) {
			$this->errors->add('error', $this->translator->trans('errors.activated'));
			return false;
		}
		
		$reminder = str_random(32);

		$this->provider->setReminder($user->id, $reminder);
		
		$me = $this;

		$this->mailer->send('emails.activation', compact('reminder'), function($message) use($user, $me) {
		    $message->to($user->email);
		    $message->subject($me->translator->trans('emails.activation_subject'));
		});

		if ($this->mailer->failures()) {
			$this->errors->add('error', trans('errors.send_email'));
			return false;
		}

		return true;
	}

	/**
	 * Activate an account with the given activation key.
	 *
	 * @param  string  $reminder
	 * @return bool
	 */
	public function activate($reminder)
	{
		$this->errors->clear();

		$validator = $this->validator->make(
			array('activation_key' => $reminder), 
			array('activation_key' => 'required|exists:users,reminder')
		);

		if ($validator->fails()) {
			$this->errors = $validator->errors();
			return false;
		}

		$user = $this->provider->get(compact('reminder'));

		if (!$this->provider->activate($user->id)) {
			$this->errors->add('error', trans('errors.dbsave'));
			return false;
		}
		
		return true;
	}

	protected function generateDisplayName($input)
	{
		if (isset($input['first_name'], $input['last_name'])) {
			$firstName = escape($input['first_name']);
			$lastName  = escape($input['last_name']);
			
			if (!empty($firstName) && !empty($lastName)) {
				return "{$firstName} {$lastName}";
			}
		}

		if (!empty($input['username'])) {
			return $input['username'];
		}
	}
}