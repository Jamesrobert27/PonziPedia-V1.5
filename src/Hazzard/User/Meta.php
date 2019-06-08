<?php namespace Hazzard\User;

use Hazzard\Database\Connection;

class Meta {

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
	protected $table = 'usermeta';

	/**
	 * Create a new user meta instance.
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * Add meta data field to a user.
	 *
	 * @param  int 		$userId 	User ID.
	 * @param  string 	$metaKey 	Metadata name.
	 * @param  mixed 	$metaValue  Metadata value.
	 * @param  bool 	$unique 	Optional, default is false. Whether the same key should not be added.
	 * @return int|false 			Meta ID on success, false on failure.
	 */
	public function add($userId, $metaKey, $metaValue, $unique = false)
	{
		if (!$this->isNumeric($userId)) return false;

		$metaKey = sanitize_key($metaKey);

		$metaValue = maybe_serialize($metaValue);

		if ($unique && $this->has($userId, $metaKey)) return false;

		$data = array(
			'user_id' => $userId,
			'meta_key' => $metaKey,
			'meta_value' => $metaValue
		);

		return $this->newQuery()->insertGetId($data);
	}

	/**
	 * Retrieve user meta field for a user.
	 *
	 * @param  int 		$userId 	User ID.
	 * @param  string 	$metaKey 	Optional. The meta key to retrieve. By default, returns data for all keys.
	 * @param  bool 	$single 	Whether to return a single value.
	 * @return mixed 				Will be an array if $single is false. Will be value of meta data field if $single is true.
	 */
	public function get($userId, $metaKey = '', $single = false)
	{
		if (!$this->isNumeric($userId)) return false;

		$metaKey = sanitize_key($metaKey);

		$query = $this->newQuery()->where('user_id', $userId);

		if ($metaKey == '') {
			$meta = $query->get();

			if (count($meta)) {
				$_meta = array();
				
				foreach ($meta as $entry) {
					if ($single) {
						$_meta[$entry->meta_key] = maybe_unserialize($entry->meta_value);
					} else {
						$_meta[$entry->meta_key][] = maybe_unserialize($entry->meta_value);
					}
				}
			}
		} else {
			$meta = $query->where('meta_key', $metaKey);

			if ($single) {
				return maybe_unserialize($query->pluck('meta_value'));
			} else {
				$meta = $query->get();

				if (count($meta)) {
					$_meta = array();
					
					foreach ($meta as $entry) {
						$_meta[] = maybe_unserialize($entry->meta_value);
					}
				}						
			}
		}

		return isset($_meta) ? $_meta : null;
	}

	/**
	 * Update user meta field based on user ID.
	 *
	 * @param  int 		$userId 	User ID.
	 * @param  string 	$metaKey 	Metadata key.
	 * @param  mixed  	$metaValue  Metadata value.
	 * @param  mixed 	$prevValue  Optional. Previous value to check before removing.
	 * @return int|bool 			Meta ID if the key didn't exist, true on successful update, false on failure.
	 */
	public function update($userId, $metaKey, $metaValue, $prevValue = '')
	{
		if (!$this->isNumeric($userId)) return false;

		$metaKey = sanitize_key($metaKey);

		$metaValue = maybe_serialize($metaValue);

		$prevValue = maybe_serialize($prevValue);

		if (!$this->has($userId, $metaKey)) {
			return $this->add($userId, $metaKey, $metaValue);
		}

		$query = $this->newQuery()->where('user_id', $userId)->where('meta_key', $metaKey);

		if ($prevValue != '') $query->where('meta_value', $prevValue);

		return $query->update(array('meta_value' => $metaValue));
	}

	/**
	* Remove metadata matching criteria from a user.
	*
	* @param  int  	  $userId 	  User ID.
	* @param  string  $metaKey    Optional. Metadata name.
	* @param  mixed   $metaValue  Optional. Metadata value.
	* @return bool  			  True on success, false on failure.
	*/
	public function delete($userId, $metaKey = '', $metaValue = '')
	{
		$metaKey = sanitize_key($metaKey);

		$query = $this->newQuery()->where('user_id', $userId);

		if ($metaKey != '') {
			$query->where('meta_key', $metaKey);
		}

		if ($metaValue != '') {
			$metaValue = maybe_serialize($metaValue);
			
			$query->where('meta_value', $metaValue);
		}

		return $query->delete();
	}

	/**
	* Check user has meta key .
	*
	* @param  int     $userId
	* @param  string  $metaKey
	* @return bool
	*/
	protected function has($userId, $metaKey)
	{
		return $this->newQuery()->where('user_id', $userId)->where('meta_key', $metaKey)->pluck('id');	
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
}