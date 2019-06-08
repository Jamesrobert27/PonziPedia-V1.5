<?php namespace Hazzard\Auth;

use Hazzard\Cookie\CookieJar;
use Hazzard\Validation\Factory;
use Hazzard\Translation\Translator;
use Hazzard\Session\Store as SessionStore;
use Hazzard\Encryption\Encrypter;

class Auth extends Manager {

	/**
	 * The currently authenticated user.
	 *
	 * @var \Hazzard\Database\Model
	 */
	protected $user;

	/**
	 * The encrypter instance.
	 *
	 * @var \Hazzard\Encryption\Encrypter
	 */
	protected $encrypter;

	/**
	 * Create a new auth instance.
	 *
	 * @param  \Hazzard\Auth\UserProvider       $provider
	 * @param  \Hazzard\Session\SessionStore    $session
	 * @param  \Hazzard\Cookie\CookieJar  	    $cookie
	 * @param  \Hazzard\Validation\Factory      $validator
	 * @param  \Hazzard\Translation\Translator  $translator
	 * @param  \Hazzard\Encryption\Encrypter    $encrypter
	 * @param  array 							$config
	 * @return void
	 */
	public function __construct(UserProvider $provider, SessionStore $session, 
				CookieJar $cookie, Factory $validator, Translator $translator, Encrypter $encrypter, array $config)
	{
		$this->cookie = $cookie;
		$this->session = $session;
		$this->provider = $provider;
		$this->encrypter = $encrypter;
		$this->validator = $validator;
		$this->translator = $translator;

		parent::__construct();
	}

	/**
	 * Determine if the current user is authenticated.
	 *
	 * @return bool
	 */
	public function check()
	{
		return !is_null($this->user());
	}

	/**
	 * Determine if the current user is a guest.
	 *
	 * @return bool
	 */
	public function guest()
	{
		return !$this->check();
	}

	/**
	 * Get the currently authenticated user.
	 *
	 * @return \Hazzard\Database\Model|null
	 */
	public function user()
	{
		if (!is_null($this->user)) return $this->user;

		$id = $this->session->get($this->getName());

		if (!is_null($id)) {
			$this->loginById($id);
		}

		if (is_null($this->user)) {
			$this->loginByCookie();
		}

		return $this->user;
	}

	/**
	 * Log user by the given email/username and password.
	 *
	 * @param  string  $email
	 * @param  string  $password
	 * @param  bool    $remember
	 * @return bool
	 */
	public function login($email, $password, $remember = false)
	{	
		$this->errors->clear();

		$data = array(
			'username' => $email,
		   	'password' => $password
		);
		
		$rules = array(
			'username' => 'required|alpha_dash',
			'password' => 'required'
		);

		$validator = $this->validator->make($data, $rules);
		
		if ($validator->fails()) {
			$data['email'] = $data['username'];
			$rules['email'] = 'required|email';

			unset($data['username'], $rules['username']);

			$validator = $this->validator->make($data, array_reverse($rules));
			
			if ($validator->fails()) {
				$this->errors = $validator->errors();
				return false;
			}
		}

		if (!$this->attempt($data, false, false)) {
			$this->errors->add('error', $this->translator->trans('errors.credentials'));
			return;
		}
		
		$user = $this->provider->get($data);

		if ($user->isSuspended()) {
			$this->errors->add('error', $this->translator->trans('errors.suspended'));
		} elseif (!$user->isActive()) {
			$this->errors->add('error', $this->translator->trans('errors.unactivated'));
		} else {
			$this->loginById($user->id, $remember);

			if (isset($this->events)) {
				$this->events->fire('auth.login', array($user, $remember));
			}

			return true;
		}

		return false;
	}

	/**
	 * Log out the currently authenticated user.
	 *
	 * @return void
	 */
	public function logout()
	{
		if (!$this->user) return;

		if (isset($this->events)) {
			$this->events->fire('auth.logout', array($this->user));
		}

		$this->provider->setRememberToken($this->user->id);

		$this->user = null;

		$this->session->destroy();

		try {
			$this->cookie->delete($this->getName());
		} catch (\Exception $e) {}
	}

	/**
	 * Determine if the current user has the specified permission.
	 *
	 * @param  string 	$permission
	 * @return bool
	 */
	public function userCan($permission)
	{
		if (!$this->user) return false;

		return $this->user->can($permission);
	}

	/**
	 * Log user by the given user ID.
	 *
	 * @param  mixed  $id
	 * @param  bool   $remember
	 * @return bool
	 */
	public function loginById($id, $remember = false)
	{	
		$user = $this->provider->get(compact('id'));

		if (is_null($user)) {
			return false;
		}

		if (!$user->isActive()) $user = null;

		if (!is_null($user)) {
			$this->loginUser($user, $remember);

			return true;
		}

		return false;
	}

	/**
	 * Attempt to authenticate a user using the given credentials.
	 *
	 * @param  array  $credentials
	 * @param  bool   $remember
	 * @param  bool   $login
	 * @return bool
	 */
	protected function attempt(array $credentials, $remember = false, $login = true)
	{
		$user = $this->provider->get($credentials);

		if (is_null($user)) return false;

		if (isset($credentials['password']) && 
			!$this->provider->validatePassword($credentials['password'], $user->password)) {
			return false;
		}

		if ($login) $this->loginUser($user, $remember);

		return true;
	}

	/**
	 * Set the current authenticated user.
	 *
	 * @param  \Hazzard\Database\Model  $user
	 * @param  bool   $remember				
	 * @return void
	 */
	protected function loginUser($user, $remember = false)
	{
		$this->user = $user;

		$this->session->set($this->getName(), $user->id);

		if ($remember) $this->cookieRemember($user->id);
	}

	/**
	 * Remember the current user via cookie.
	 *
	 * @param  int  $userId
	 * @return void
	 */
	protected function cookieRemember($userId)
	{
		$minutes = 60*24*30;

		$remember = (time()+$minutes*60).str_random(32);

		$this->provider->setRememberToken($userId, $remember);

		$this->cookie->set($this->getName(), $this->encrypter->encrypt($remember), $minutes);
	}

	/**
	 * Log user by cookie remember token.
	 *
	 * @return void
	 */
	protected function loginByCookie()
	{
		$remember = $this->cookie->get($this->getName());

		if (is_null($remember)) return;

		try {
			$remember = $this->encrypter->decrypt($remember);
		} catch(\DecryptException $e) {}

		if ((int) substr($remember, 0, 10) < time()) return;

		$user = $this->provider->get(compact('remember'));

		if (!is_null($user)) $this->loginById($user->id);

		try {
			if (is_null($this->user)) $this->cookie->delete($this->getName());
		} catch (\Exception $e) {}

		if (!is_null($this->user) && isset($this->events)) {
			$this->events->fire('auth.login', array($this->user, $remember));
		}
	}

	/**
	 * Get a unique identifier for the auth session value.
	 *
	 * @return string
	 */
	public function getName()
	{
		return 'login_'.md5(get_class($this));
	}
}