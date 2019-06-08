<?php namespace Hazzard\Auth;

use Hazzard\Validation\Factory;
use Hazzard\Translation\Translator;

class PasswordReminder extends Manager {

	/**
	 * Create a new reminder instance.
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
	 * Send the password reminder.
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
		} elseif (!$user->isActive()) {
			$this->errors->add('error', $this->translator->trans('errors.unactivated'));
			return false;
		}
		
		$reminder = time() . str_random(32);

		$this->provider->setReminder($user->id, $reminder);

		$me = $this;
		
		$this->mailer->send('emails.reminder', compact('reminder'), function($message) use($user, $me) {
		    $message->to($user->email);
		    $message->subject($me->translator->trans('emails.reminder_subject'));
		});

		if ($this->mailer->failures()) {
			$this->errors->add('error', trans('errors.send_email'));
			return false;
		}

		return true;
	}

	/**
	 * Reset passsword with a given reminder.
	 *
	 * @param  string  $pass1
	 * @param  string  $pass2
	 * @param  string  $reminder
	 * @return bool
	 */
	public function reset($pass1, $pass2, $reminder)
	{
		// Extend the validator to validate if the reminder was
		// generated in the last 10 days
		$this->validator->extend('valid', function($attribute, $value, $parameters) {
			$time = 60*60*24*10;
			
			return (int) substr($value, 0, 10) + $time > time();
		});

		$validator = $this->validator->make(
			array(
				'reminder' => $reminder,
				'password' => $pass1,
    			'password_confirmation' => $pass2,
			), 
			array(
				'reminder' => 'required|valid|exists:users,reminder',
				'password' => 'required|between:4,30|confirmed'
			)
		);

		if ($validator->fails()) {
			$this->errors = $validator->errors();
			return false;
		}
		
		$user = $this->provider->get(compact('reminder'));

		if (!$this->provider->setPassword($user->id, $pass1)) {
			$this->setError(trans('errors.dbsave'));
			return false;
		}

		return true;
	}
}