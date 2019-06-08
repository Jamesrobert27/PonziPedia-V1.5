<?php namespace Hazzard\Auth;

use Hazzard\Validation\Factory;
use Hazzard\Translation\Translator;

class OAuth extends Manager {

	/**
	 * The auth instance.
	 *
	 * @var \Hazzard\Auth\Auth
	 */
	protected $auth;

	/**
	 * Create a new oauth instance.
	 *
	 * @param  \Hazzard\Auth\UserProvider  $provider
	 * @param  \Hazzard\Validation\Factory  $validator
	 * @param  \Hazzard\Translation\Translator  $translator
	 * @param  \Hazzard\Auth\Auth  $auth
	 * @param  array  $config
	 * @return void
	 */
	public function __construct(UserProvider $provider, Factory $validator, Translator $translator, Auth $auth, array $config)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->provider = $provider;
		$this->validator = $validator;
		$this->translator = $translator;

		parent::__construct();
	}

	/**
	 * Log user by the given oauth method and id.
	 *
	 * @param  string  $method
	 * @param  string  $id
	 * @param  bool    $remember
	 * @return bool
	 */
	public function login($method, $id, $remember = true)
	{
		$this->errors->clear();

		$userId = $this->provider->usermeta()->newQuery()
						->where('meta_key', "{$method}_id")
						->where('meta_value', $id)
						->pluck('user_id');

		if (!is_null($userId)) {
			$user = $this->provider->get(array('id' => $userId));
			
			if (!is_null($user)) {
				if ($user->isSuspended()) {
					$this->errors->add('error', $this->translator->trans('errors.suspended'));
				} elseif (!$user->isActive()) {
					$this->errors->add('error', $this->translator->trans('errors.unactivated'));
				} else {
					$this->auth->loginById($user->id, $remember);

					return true;
				}

				return false;
			}
		}

		$this->errors->add('error', $this->translator->trans('errors.credentials'));

		return false;
	}

	/**
	 * Create a new user account.
	 *
	 * @param  string  $method
	 * @param  array   $input
	 * @return bool
	 */
	public function signup($method, array $input)
	{
		$this->errors->clear();

		$data = array(
	    	'email' => $input['email']
	    );

		$rules = array(
	    	'email' => 'required|email|max:100|unique:users'
	    );

	    if ($this->config['require_username']) {
	    	$data['username'] = $input['username'];
	    	$rules['username'] = 'required|min:3|max:50|alpha_dash|unique:users';
	    }

		foreach ($this->userfields->all('signup') as $key => $field) {
    		if (!empty($field['validation'])) {
	    		$data[$key] = @$input['usermeta'][$key];
	    		$rules[$key] = $field['validation'];
	    	}
	    }

	    $validator = $this->validator->make($data, $rules);

		if ($validator->fails()) {
			$this->errors = $validator->errors();
			return false;
		}

		$data = $roles = array();

		foreach ($this->userfields->all('user') as $key => $field) {
			if (isset($input['usermeta'][$key]) && !empty($field['validation'])) {
	    		$data[$key] = @$input['usermeta'][$key];
	    		$rules[$key] = $field['validation'];
	    	}
	    }

		$validator = $this->validator->make($data, $rules);

		if ($validator->fails()) {
			foreach ($validator->failed() as $key => $f) {
				unset($input['usermeta'][$key]);
			}
		}

		$user = array(
			'status' => 1,
			'email' => $input['email'],
			'role_id' => $this->config['default_role_id'],
			'display_name' => $this->generateDisplayName($input),
			'usermeta' => $input['usermeta'],
		);
		
		if ($this->config['require_username']) {
			$user['username'] = $input['username'];
		}

		$user = $this->provider->create($user);

		if (isset($this->events)) {
			$this->events->fire('auth.signup', array($user));
		}

		return true;
	}

	protected function generateDisplayName($input)
	{
		if (isset($input['usermeta']['first_name'], $input['usermeta']['last_name'])) {
			$firstName = escape($input['usermeta']['first_name']);
			$lastName = escape($input['usermeta']['last_name']);
			
			if (!empty($firstName) && !empty($lastName)) {
				return "{$firstName} {$lastName}";
			}
		}

		if (!empty($input['username'])) {
			return $input['username'];
		}
	}

}