<?php namespace Hazzard\Database;

use PDO;

class Connection {

	/**
	 * The active PDO connection.
	 *
	 * @var PDO
	 */
	protected $pdo;

	/**
	 * The default fetch mode of the connection.
	 *
	 * @var int
	 */
	protected $fetchMode = PDO::FETCH_CLASS;

	/**
	 * The table prefix for the connection.
	 *
	 * @var string
	 */
	protected $tablePrefix = '';

	/**
	 * Create a new connection instance.
	 * 
	 * @param  array  $config
	 * @return void
	 */
	public function __construct(array $config)
	{
		$this->pdo = $this->createConnection($config);
		
		$this->tablePrefix = $config['prefix'];
	}

	/**
	 * Create a new PDO connection.
	 *
	 * @param  array   $config
	 * @return PDO
	 */
	public function createConnection(array $config)
	{
		extract($config);

		$dsn = "$driver:host={$hostname};dbname={$database}";

		$options = array(
			PDO::MYSQL_ATTR_INIT_COMMAND => "set names {$charset} collate {$collation}"
		);

		return new PDO($dsn, $username, $password, $options);
	}

	/**
	 * Begin a fluent query against a database table.
	 *
	 * @param  string  $table
	 * @return \Hazzard\Database\Query
	 */
	public function table($table)
	{
		$query = new Query($this);

		return $query->from($table);
	}

	/**
	 * Get a new raw query expression.
	 *
	 * @param  mixed  $value
	 * @return \Hazzard\Database\Expression
	 */
	public function raw($value)
	{
		return new Expression($value);
	}

	/**
	 * Run a select statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return array
	 */
	public function select($query, $bindings = array())
	{
		$statement = $this->getPdo()->prepare($query);

		$statement->execute($bindings);
		
		return $statement->fetchAll($this->getFetchMode());
	}

	/**
	 * Run an insert statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function insert($query, $bindings = array())
	{
		return $this->statement($query, $bindings);
	}

	/**
	 * Run an update statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function update($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Run a delete statement against the database.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function delete($query, $bindings = array())
	{
		return $this->affectingStatement($query, $bindings);
	}

	/**
	 * Execute an SQL statement and return the boolean result.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return bool
	 */
	public function statement($query, $bindings = array())
	{
		return $this->getPdo()->prepare($query)->execute($bindings);
	}

	/**
	 * Run an SQL statement and get the number of rows affected.
	 *
	 * @param  string  $query
	 * @param  array   $bindings
	 * @return int
	 */
	public function affectingStatement($query, $bindings = array())
	{
		$statement = $this->getPdo()->prepare($query);

		$statement->execute($bindings);

		return $statement->rowCount();
	}

	/**
	 * Get the table prefix for the connection.
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		return $this->tablePrefix;
	}

	/**
	 * Set the table prefix in use by the connection.
	 *
	 * @param  string  $prefix
	 * @return void
	 */
	public function setTablePrefix($prefix)
	{
		$this->tablePrefix = $prefix;
	}

	/**
	 * Get the PDO instance.
	 *
	 * @return PDO
	 */
	public function getPdo()
	{
		return $this->pdo;
	}

	/**
	 * Get the default fetch mode for the connection.
	 *
	 * @return int
	 */
	public function getFetchMode()
	{
		return $this->fetchMode;
	}

	/**
	 * Set the default fetch mode for the connection.
	 *
	 * @param  int  $fetchMode
	 * @return int
	 */
	public function setFetchMode($fetchMode)
	{
		$this->fetchMode = $fetchMode;
	}
}
