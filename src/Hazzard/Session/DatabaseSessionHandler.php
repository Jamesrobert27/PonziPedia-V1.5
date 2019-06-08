<?php namespace Hazzard\Session;

use Hazzard\Database\Connection;

class DatabaseSessionHandler {

	/**
	 * The database connection instance.
	 *
	 * @var \Hazzard\Database\Connection
	 */
	protected $db;

	/**
	 * The database table.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The session lifetime.
	 *
	 * @var int
	 */
	protected $lifetime;
	
	/**
	 * Create a new instance.
	 * 
	 * @param  \Hazzard\Database\Connection  $db
	 * @param  string 	$table
	 * @param  int 		$lifetime
	 * @return void
	 */
	function __construct(Connection $db, $table, $lifetime)
	{
		$this->db = $db;

		$this->table = $table;

		$this->lifetime = $lifetime;

		session_set_save_handler(
			array($this, 'open'),
		    array($this, 'close'),
		    array($this, 'read'),
		    array($this, 'write'),
		    array($this, 'destroy'),
		    array($this, 'gc')
		);

		register_shutdown_function('session_write_close');
	}

	/**
	 * Database open handler.
	 * 
	 * @return bool
	 */
	public function open()
	{ 
		return true; 
	}

	/**
	 * Database close handler.
	 * 
	 * @return bool
	 */
	public function close()
	{ 
		return true; 
	}

	/**
	 * Database read handler.
	 * 
	 * @param  int  $id
	 * @return string
	 */
	public function read($id)
	{
	   $data = $this->newQuery()
					->where('id', $id)
					->pluck('payload');

		return $data === null ? '' : $data;
	}

	/**
	 * Database write handler.
	 * 
	 * @param  int  	$id
	 * @param  string 	$data
	 * @return string
	 */
	public function write($id, $data)
	{
		$last_activity = time();

		return $this->newQuery()->replace(array(
			'id' => $id,
			'payload' => $data,
			'last_activity' => $last_activity
		));
	}

	/**
	 * Database destroy handler.
	 * 
	 * @param  int  $id
	 * @return string
	 */
	public function destroy($id)
	{
		return $this->newQuery()
					->where('id', $id)
					->limit(1)
					->delete();
	}

	/**
	 * Database gc handler.
	 * 
	 * @param  int  $lifetime
	 * @return string
	 */
	public function gc($lifetime)
	{
		if (empty($this->lifetime)) {
			$this->lifetime = $lifetime;
		}

		$time = time() - $this->lifetime;

		return $this->newQuery()
					->where('last_activity', '<', $time)
					->delete();
	}

	/**
	 * Create new database query.
	 * 
	 * @return \Hazzard\Database\Query
	 */
	protected function newQuery()
	{
		return $this->db->table($this->table);
	}
}