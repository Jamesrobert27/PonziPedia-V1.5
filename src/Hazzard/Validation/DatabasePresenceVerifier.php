<?php namespace Hazzard\Validation;

use Hazzard\Database\Connection;

class DatabasePresenceVerifier {

	/**
	 * The database connection instance.
	 *
	 * @var  \Hazzard\Database\Connection
	 */
	protected $db;

	/**
	 * Create a new database presence verifier.
	 *
	 * @param  \Hazzard\Database\Connection  $db
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * Count the number of objects in a collection having the given value.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  string  $value
	 * @param  int     $excludeId
	 * @param  string  $idColumn
	 * @param  array   $extra
	 * @return int
	 */
	public function getCount($collection, $column, $value, $excludeId = null, $idColumn = null, array $extra = array())
	{
		$query = $this->table($collection)->where($column, '=', $value);

		if (!is_null($excludeId) && $excludeId != 'NULL') {
			$query->where($idColumn ?: 'id', '<>', $excludeId);
		}

		foreach ($extra as $key => $extraValue) {
			$this->addWhere($query, $key, $extraValue);
		}

		return $query->count();
	}

	/**
	 * Count the number of objects in a collection with the given values.
	 *
	 * @param  string  $collection
	 * @param  string  $column
	 * @param  array   $values
	 * @param  array   $extra
	 * @return int
	 */
	public function getMultiCount($collection, $column, array $values, array $extra = array())
	{
		$query = $this->table($collection)->whereIn($column, $values);

		foreach ($extra as $key => $extraValue) {
			$this->addWhere($query, $key, $extraValue);
		}

		return $query->count();
	}

	/**
	 * Add a "where" clause to the given query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  string  $key
	 * @param  string  $extraValue
	 * @return void
	 */
	protected function addWhere($query, $key, $extraValue)
	{
		if ($extraValue === 'NULL') {
			$query->whereNull($key);
		} elseif ($extraValue === 'NOT_NULL') {
			$query->whereNotNull($key);
		} else {
			$query->where($key, $extraValue);
		}
	}

	/**
	 * Get a query builder for the given table.
	 *
	 * @param  string  $table
	 * @return \Hazzard\Database\Query
	 */
	protected function table($table)
	{
		return $this->db->table($table);
	}
}