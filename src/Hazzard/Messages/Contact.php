<?php namespace Hazzard\Messages;

use Hazzard\Auth\UserProvider;
use \Hazzard\Database\Connection;

class Contact {

	/**
	 * Database connection instance.
	 * 
	 * @return \Hazzard\Database\Connection
	 */
	protected $db;

	/**
	 * Database table name.
	 * 
	 * @var string
	 */
	protected $table = 'contacts';

	/**
	 * User provider instance.
	 * 
	 * @return \Hazzard\Auth\UserProvider
	 */
	protected $userProvider;

	/**
	 * Create a contact instance.
	 *
	 * @param  \Hazzard\Database\Connection
	 * @param  \Hazzard\Auth\Auth
	 * @return void
	 */
	public function __construct(Connection $db, UserProvider $provider)
	{
		$this->db = $db;
		$this->userProvider = $provider;
	}

	/**
	* Request contact.
	*
	* @param  int  $user1
	* @param  int  $user2
	* @return bool
	*/
	public function add($user1, $user2)
	{
		$contact = $this->find($user1, $user2);

		if ($contact === false || $user1 == $user2) return false;

		if (is_null($contact)) {
			return $this->newQuery()->insert(compact('user1', 'user2'));
		}

		if (empty($contact->accepted) && $contact->user2 == $user1) {
			return $this->confirm($user1, $user2);
		}

		return false;
	}

	/**
	* Confirm contact request.
	*
	* @param  int  $user1
	* @param  int  $user2
	* @return bool
	*/
	public function confirm($user1, $user2)
	{
		$contact = $this->find($user1, $user2);

		if (empty($contact) || !empty($contact->accepted)) return false;

		if ($contact->user1 == $user2 && $contact->user2 == $user1) {
			return $this->newQuery()->where('id', $contact->id)
					->limit(1)->update(array('accepted' => 1));
		}

		return false;
	}

	/**
	* Remove contact.
	*
	* @param  int  $user1
	* @param  int  $user2
	* @return bool
	*/
	public function remove($user1, $user2)
	{
		$contact = $this->find($user1, $user2);

		if (empty($contact)) return false;

		return $this->newQuery()->where('id', $contact->id)->limit(1)->delete();
	}

	/**
	* Find contact.
	*
	* @param  int  $user1
	* @param  int  $user2
	* @return array|null|false
	*/
	public function find($user1, $user2)
	{
		if (!$this->isNumeric($user1) || !$this->isNumeric($user2)) return false;

		$query = $this->newQuery()
			->where(function($q) use($user1, $user2) {
				$q->where('user1', $user1)->where('user2', $user2);
			})
			->orWhere(function($q) use($user1, $user2) {
				$q->where('user1', $user2)->where('user2', $user1);
			})
			->limit(1);

		return $query->first();
	}

	/**
	* Check if two users are contact and return the result.
	*
	* @param  int  $user1
	* @param  int  $user2
	* @return array|null|false
	*/
	public function check($user1, $user2)
	{
		$contact = $this->find($user1, $user2);

		return !(empty($contact) || empty($contact->accepted));
	}

	/**
	* Delete all contacts for the given user.
	*
	* @param  int  $userId
	* @return bool
	*/
	public function deleteAll($userId)
	{
		return $this->newQuery()->where('user1', $userId)
					->orWhere('user2', $userId)->delete();
	}

	/**
	* Get all contacts for the given user.
	*
	* @param  int  $userId
	* @return array
	*/
	public function all($userId)
	{
		$model = $this->userProvider->userModel();

		$query = $model->select("{$model->getTable()}.id as id", 
								'username', 'display_name', 'email', 'accepted');

		$query->join($this->getTable(), function($join) use($model) {
				$join->on("{$model->getTable()}.id", '=', 'user1')
						->orOn("{$model->getTable()}.id", '=', 'user2');
			})
			->where(function($q) use($userId) {
				$q->where(function($q) use($userId) {
					$q->where('user1', $userId)->where('accepted', 1);
				})
				->orWhere('user2', $userId);
			})
			->where("{$model->getTable()}.id", '!=', $userId)
			->where('status', 1)
			->orderBy('accepted', 'asc');

		$contacts = array();

		foreach ($query->get() as $contact) {
			$contacts[] = array(
				'id' => $contact->id,
				'name' => empty($contact->display_name)?$contact->email:$contact->display_name,
				'avatar' => $contact->avatar,
				'username' => $contact->username,
				'accepted' => (bool) $contact->accepted,
			);
		}

		return $contacts;
	}

	/**
	* Check if the value is non empty numeric.
	*
	* @param  mixed  $value
	* @return bool
	*/
	protected function isNumeric($value)
	{
		return (!empty($value) && is_numeric($value));
	}

	/**
	* Create a new query.
	*
	* @return \Hazzard\Database\Query
	*/
	public function newQuery()
	{
		return $this->db->table($this->table);
	}

	/**
	* Set the working table.
	*
	* @param  string  $table
	* @return void
	*/
	public function setTable($table)
	{
		$this->table = $table;
	}

	/**
	* Get the working table.
	*
	* @return string
	*/
	public function getTable()
	{
		return $this->table;
	}
}