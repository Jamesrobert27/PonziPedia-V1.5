<?php namespace Hazzard\Messages;

use DateTime;
use Hazzard\Auth\UserProvider;
use Hazzard\Validation\Factory;
use Hazzard\Database\Connection;

class Message {

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
	protected $table = 'messages';

	/**
	 * User provider instance.
	 * 
	 * @return \Hazzard\Auth\UserProvider
	 */
	protected $userProvider;

	/**
	 * Validation factory instance.
	 * 
	 * @return \Hazzard\Validation\Factory
	 */
	protected $validator;

	/**
	 * Create a message instance.
	 *
	 * @param  \Hazzard\Database\Connection  $db
	 * @param  \Hazzard\Auth\UserProvider 	 $provider
	 * @param  \Hazzard\Validation\Factory 	 $validator
	 * @return void
	 */
	public function __construct(Connection $db, UserProvider $provider, Factory $validator)
	{
		$this->db = $db;
		$this->validator = $validator;
		$this->userProvider = $provider;
	}

	/**
	 * Get the number of unread messages for the gived user.
	 *
	 * @param  int  $userId
	 * @return int
	 */
	public function countUnread($userId)
	{
		if (!$this->isNumeric($userId)) return 0;

		return $this->newQuery()
					->where('to_user', $userId)
					->where('read', 0)
					->where('deleted', '!=', 2)
					->count('id');
	}

	/**
	 * Send message to the given user from the logged user.
	 *
	 * @param  int  $from
	 * @param  int  $to
	 * @return array|\Hazzard\Support\MessageBag|false
	 */
	public function send($from, $to, $message, $maxlength = null)
	{
		$table = $this->userProvider->userModel()->getTable();

		$validator = $this->validator->make(
			compact('from', 'to', 'message'), 
			array(
				'to' => "required|exists:{$table},id",
				'from' => 'required|numeric',
				'message' => 'required'.($maxlength ? "|max:{$maxlength}" : '')
			)
		);

		if ($validator->fails()) return $validator->errors();
		
		$message = array(
			'to_user' => $to,
			'from_user' => $from,
			'message' => escape($message),
			'date' => with(new DateTime())->format('Y-m-d H:i:s')
		);

		$id = $this->newQuery()->insertGetId($message);

		if (!$id) return false;

		$message = $this->newQuery()->find($id);
		
		$user = $this->findUser($from);
		
		return array(
			'id' => $message->id,
			'user' => array(
				'id' => $user->id,
				'name' => $user->display_name,
				'avatar' => $user->avatar,
			),
			'sent' => true,
			'message' => $message->message,
			'timestamp' => $message->date,
		);
	}

	/**
	 * Get the conversations for the gived user.
	 *
	 * @param  int   $userId
	 * @return array
	 */
	public function getConversations($userId)
	{
		if (!$this->isNumeric($userId)) return array();

		$query = $this->newQuery()
			->where(function($q) use($userId) {
				$q->where('to_user', $userId)->orWhere('from_user', $userId);
			})
			->where(function($q) use($userId) {
				$q->where(function($q) use($userId) {
					$q->where('deleted', '!=', 1)->where('from_user', $userId);
				})
				->orWhere(function($q) use($userId) {
					$q->where('deleted', '!=', 2)->where('to_user', $userId);
				});
			})
			->orderBy('date', 'desc');

		$conversations = array();

		foreach ($query->get() as $message) {
			$id = ($message->from_user == $userId) ? $message->to_user : $message->from_user;
			$user = $this->findUser($id);

			if (!$user || isset($conversations[$user->id])) continue;

			$conversations[$user->id] = array(
				'id' => $message->id,
				'user' => array(
					'id' => $user->id,
					'name' => $user->display_name,
					'avatar' => $user->avatar,
				),
				'read' => (bool) $message->read,
				'message' => $this->substr($message->message),
				'replied' => $message->from_user == $userId,
				'timestamp' => $message->date,
			);
		}

		return array_values($conversations);
	}

	/**
	 * Get the conversation messages between two users.
	 *
	 * @param  int          $user1
	 * @param  int          $user2
	 * @param  string|null  $timestamp
	 * @return array
	 */
	public function getConversation($user1, $user2, $timestamp = null)
	{
		if (!$this->isNumeric($user2) || !$this->isNumeric($user1)) return array();

		// Mark the unread conversation messages as read.
		$this->newQuery()
			->where('to_user', $user1)
			->where('from_user', $user2)
			->update(array('read' => 1));

		$query = $this->newQuery()
			->where(function($q) use($user1, $user2) {
				$q->where(function($q) use($user1, $user2) {
					$q->where('to_user', $user1)->where('from_user', $user2);
				})
				->orWhere(function($q) use($user1, $user2) {
					$q->where('from_user', $user1)->where('to_user', $user2);
				});
			})
			->where(function($q) use($user1) {
				$q->where(function($q) use($user1) {
					$q->where('deleted', '!=', 1)->where('from_user', $user1);
				})
				->orWhere(function($q) use($user1) {
					$q->where('deleted', '!=', 2)->where('to_user', $user1);
				});
			});

		if (!empty($timestamp)) {
			$query = $this->newQuery()
				->where('to_user', $user1)
				->where('from_user', $user2)
				->where('deleted', '!=', 2)
				->where('date', '>', $timestamp);
		}

		$query = $query->orderBy('date', 'asc');

		$messages = array();

		foreach ($query->get() as $message) {
			$id = ($message->from_user == $user1) ? $user1 : $message->from_user;
			$user = $this->findUser($id);

			if (!$user) continue;

			$messages[] = array(
				'id' => $message->id,
				'user' => array(
					'id' => $user->id,
					'name' => $user->display_name,
					'avatar' => $user->avatar,
				),
				'sent' => $message->from_user == $user1,
				'message' => $message->message,
				'timestamp' => $message->date,
				
				
			);
		}

		return $messages;
	}

	/**
	 * Delete message or messages for the given user.
	 *
	 * @param  int       $userId
	 * @param  int|null  $messageId
	 * @return bool
	 */
	public function delete($userId, $messageId = null)
	{
		if (!$this->isNumeric($userId)) return false;

		$query = $this->newQuery();

		if ($this->isNumeric($messageId)) {
			$query->where('id', $messageId)->limit(1);
		}

		$query->where(function($q) use($userId) {
			$q->where('to_user', $userId)->orWhere('from_user', $userId);
		});

		foreach ($query->get() as $message) {
			if (empty($message->deleted)) {
				$deleted = ($message->from_user == $userId) ? 1 : 2;
				
				$this->newQuery()->where('id', $message->id)->limit(1)
						->update(compact('deleted'));
			} else {
				$this->newQuery()->where('id', $message->id)
						->limit(1)->delete();
			}
		}

		return true;
	}

	/**
	 * Delete all messages between two users.
	 *
	 * @param  int       $user1
	 * @param  int|null  $user2
	 * @return bool
	 */
	public function deleteConversation($user1, $user2)
	{
		$values = array();

		foreach ($this->getConversation($user1, $user2) as $message) {
			$values[] = $message['id'];
		}

		if (count($values)) {
			return $this->newQuery()
					->limit(count($values))
					->whereIn('id', array_values($values))
					->delete();
		}

		return false;
	}

	/**
	 * Mark all messages as read.
	 *
	 * @param  int|null  $userId
	 * @return bool
	 */
	public function markAllAsRead($userId)
	{
		if (!$this->isNumeric($userId)) return false;

		return $this->newQuery()
					->where('to_user', $userId)
					->update(array('read' => 1));
	}

	/**
	 * Find user by the given id.
	 *
	 * @param  int  $id
	 * @return mixed
	 */
	protected function findUser($id)
	{
		return $this->userProvider->userModel()->find($id);
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
	* Return the first $maxLength characters of the string.
	*
	* @param  mixed  $value
	* @return bool
	*/
	protected function substr($message, $maxLength = 400)
	{
		if (mb_strlen($message) > $maxLength) {
			return mb_substr($message, 0, $maxLength);
		}

		return $message;
	}

	/**
	* Check if the user exceeded the sent message limit.
	*
	* @param  int  $limit
	* @param  \Hazzard\Session\Store  $session
	* @return bool
	*/
	public function limitExceed($limit, $session)
	{
		$sent = $session->get('_sent_messages');
		
		if (isset($sent['count'], $sent['time'])) {
			if ($sent['count'] > $limit) {
				if ($sent['time']+60*60 > time()) {
					return true;
				} else { 
					$sent = array('count' => 0, 'time' => time());
				}
			}

			$sent['count']++;
		} else {
			$sent = array('count' => 1, 'time' => time());
		}

		$session->set('_sent_messages', $sent);

		return false;
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
	* Get the table.
	*
	* @return string
	*/
	public function getTable()
	{
		return $this->table;
	}	
}
