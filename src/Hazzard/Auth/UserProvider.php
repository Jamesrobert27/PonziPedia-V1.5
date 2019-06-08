<?php namespace Hazzard\Auth;

use Hazzard\Hashing\HasherInterface;
use Hazzard\Database\Connection;
use Hazzard\User\Meta as Usermeta;

class UserProvider {

	/**
	 * The hasher instance.
	 *
	 * @var \Hazzard\Hashing\HasherInterface
	 */
	protected $hasher;

	/**
	 * The usermeta instance.
	 *
	 * @var \Hazzard\User\Meta
	 */
	protected $usermeta;

	/**
	 * The user model name.
	 *
	 * @var string
	 */
	protected $userModel = 'User';

	/**
	 * The role model name.
	 *
	 * @var string
	 */
	protected $roleModel = 'Role';

	/**
	 * Create a new authentication instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
	}

	/**
	 * Get a user by the given data.
	 *
	 * @param  array  $data
	 * @return \Hazzard\Database\Model|null
	 */
	public function get(array $data)
	{
		$query = $this->userModel()->newQuery();

		foreach ($data as $key => $value) {
			if ($key != 'password') $query->where($key, $value);
		}

		return $query->first();
	}

	/**
	 * Create a new user.
	 *
	 * @param  array  $data
	 * @return \Hazzard\Database\Model|null
	 */
	public function create(array $data)
	{
		if (isset($data['password'])) {
			$data['password'] = $this->hasher->make($data['password']);
		}

		if (isset($data['usermeta'])) {
			$usermeta = $data['usermeta']; 

			unset($data['usermeta']);
		}

		$userId = $this->userModel()->newQuery()->insertGetId($data);

		if ($userId) {
			if (isset($usermeta)) {
				$this->addMeta($userId, $usermeta);
			}

			return $this->userModel()->find($userId);
		}

		return null;
	}

	/**
	 * Add meta to the new user.
	 *
	 * @param  int    $userId
	 * @param  array  $meta
	 * @return bool
	 */
	protected function addMeta($userId, array $meta)
	{
		$data = array();

		$query = $this->usermeta->newQuery();

		foreach ($meta as $key => $value) {
			$data[] = array(
				'user_id' => $userId,
				'meta_key' => $key,
				'meta_value' => $value
			);
		}

		return $query->insert($data);
	}

	/**
	 * Activate user.
	 *
	 * @param  int    $userId
	 * @return bool
	 */
	public function activate($userId)
	{
		return $this->userModel()->newQuery()
					->where('id', $userId)
					->limit(1)
					->update(array('status' => 1, 'reminder' => ''));
	}

	/**
	 * Set user password.
	 *
	 * @param  int     $userId
	 * @param  string  $password
	 * @return bool
	 */
	public function setPassword($userId, $password)
	{
		$password = $this->hasher->make($password);
		$reminder = time();

		return $this->userModel()->newQuery()
					->where('id', $userId)
					->limit(1)
					->update(compact('password', 'reminder'));
	}

	/**
	 * Validate a user against the given password.
	 *
	 * @param  string  $plain
	 * @param  string  $password
	 * @return bool
	 */
	public function validatePassword($plain, $password)
	{
		return $this->hasher->check($plain, $password);
	}

	/**
	 * Set the user remember token.
	 *
	 * @param  int     $userId
	 * @param  string  $remember
	 * @return bool
	 */
	public function setRememberToken($userId, $remember = '')
	{
		return $this->userModel()->newQuery()
					->where('id', $userId)
					->limit(1)
					->update(compact('remember'));
	}

	public function setReminder($userId, $reminder)
	{
		return $this->userModel()->newQuery()
					->where('id', $userId)
					->limit(1)
					->update(compact('reminder'));
	}

	/**
	 * Set the hasher instance.
	 *
	 * @param  \Hazzard\Hasing\HasherInterface  $hasher
	 * @return $this
	 */
	public function setHasher(HasherInterface $hasher)
	{
		$this->hasher = $hasher;

		return $this;
	}

	/**
	 * Set the usermeta instance.
	 *
	 * @param  \Hazzard\User\Meta  $usermeta
	 * @return $this
	 */
	public function setUsermeta(Usermeta $usermeta)
	{
		$this->usermeta = $usermeta;

		return $this;
	}

	/**
	 * Create a new instance of the user model.
	 *
	 * @return \Hazzard\Database\Model
	 */
	public function userModel()
	{
		$class = '\\'.ltrim($this->userModel, '\\');
		
		return new $class;
	}

	/**
	 * Create a new instance of the role model.
	 *
	 * @return \Hazzard\Database\Model
	 */
	public function roleModel()
	{
		$class = '\\'.ltrim($this->roleModel, '\\');
		
		return new $class;
	}

	/**
	 * Get the hasher instance.
	 *
	 * @return \Hazzard\Hashing\HasherInterface
	 */
	public function hasher()
	{
		return $this->hasher;
	}

	/**
	 * Get the usermeta instance.
	 *
	 * @return \Hazzard\User\Meta
	 */
	public function usermeta()
	{
		return $this->usermeta;
	}
}