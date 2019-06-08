<?php namespace Hazzard\Database;

use PDO;
use Closure;

class Query {

	/**
	 * The database connection instance.
	 * 
	 * @var \Hazzard\Database\Connection
	 */
	protected $db;

	/**
	 * The model being queried.
	 *
	 * @var \Hazzard\Database\Model
	 */
	protected $model;

	/**
	 * The current query value bindings.
	 *
	 * @var array
	 */
	protected $bindings = array();

	/**
	 * An aggregate function and column to be run.
	 *
	 * @var array
	 */
	public $aggregate;

	/**
	 * The columns that should be returned.
	 *
	 * @var array
	 */
	public $columns;

	/**
	 * Indicates if the query returns distinct results.
	 *
	 * @var bool
	 */
	public $distinct = false;

	/**
	 * The table which the query is targeting.
	 *
	 * @var string
	 */
	public $from;

	/**
	 * The table joins for the query.
	 *
	 * @var array
	 */
	public $joins;

	/**
	 * The where constraints for the query.
	 *
	 * @var array
	 */
	public $wheres;

	/**
	 * The groupings for the query.
	 *
	 * @var array
	 */
	public $groups;

	/**
	 * The having constraints for the query.
	 *
	 * @var array
	 */
	public $havings;

	/**
	 * The orderings for the query.
	 *
	 * @var array
	 */
	public $orders;

	/**
	 * The maximum number of records to return.
	 *
	 * @var int
	 */
	public $limit;

	/**
	 * The number of records to skip.
	 *
	 * @var int
	 */
	public $offset;

	/**
	 * The keyword identifier wrapper format.
	 *
	 * @var string
	 */
	protected $wrapper = '`%s`';

	/**
	 * All of the available clause operators.
	 *
	 * @var array
	 */
	protected $operators = array(
		'=', '<', '>', '<=', '>=', '<>', '!=',
		'like', 'not like', 'between', 'ilike',
		'&', '|', '^', '<<', '>>',
	);

	/**
	 * Create a new query instance.
	 * 
	 * @return void
	 */
	public function __construct(Connection $db)
	{
		$this->db = $db;
	}

	/**
	 * Set the columns to be selected.
	 * 
	 * @param  array  $columns
	 * @return \Hazzard\Database\Query
	 */
	public function select($columns = array('*'))
	{
		$this->columns = is_array($columns) ? $columns : func_get_args();

		return $this;
	}

	/**
	 * Add a new "raw" select expression to the query.
	 *
	 * @param  string  $expression
	 * @return \Hazzard\Database\Query
	 */
	public function selectRaw($expression)
	{
		return $this->select(new Expression($expression));
	}

	/**
	 * Force the query to only return distinct results.
	 *
	 * @return \Hazzard\Database\Query
	 */
	public function distinct()
	{
		$this->distinct = true;

		return $this;
	}

	/**
	 * Set the table which the query is targeting.
	 * 
	 * @param  string  $table
	 * @return \Hazzard\Database\Query
	 */
	public function from($table)
	{
		$this->from = $table;

		return $this;
	}

	/**
	 * Add a join clause to the query.
	 *
	 * @param  string  $table
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $two
	 * @param  string  $type
	 * @param  bool  $where
	 * @return \Hazzard\Database\Query
	 */
	public function join($table, $one, $operator = null, $two = null, $type = 'inner', $where = false)
	{
		if ($one instanceof Closure) {
			$this->joins[] = new JoinClause($this, $type, $table);

			call_user_func($one, end($this->joins));
		} else {
			$join = new JoinClause($this, $type, $table);

			$this->joins[] = $join->on(
				$one, $operator, $two, 'and', $where
			);
		}

		return $this;
	}

	/**
	 * Add a "join where" clause to the query.
	 *
	 * @param  string  $table
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $two
	 * @param  string  $type
	 * @return \Hazzard\Database\Query
	 */
	public function joinWhere($table, $one, $operator, $two, $type = 'inner')
	{
		return $this->join($table, $one, $operator, $two, $type, true);
	}

	/**
	 * Add a left join to the query.
	 *
	 * @param  string  $table
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $second
	 * @return \Hazzard\Database\Query
	 */
	public function leftJoin($table, $first, $operator = null, $second = null)
	{
		return $this->join($table, $first, $operator, $second, 'left');
	}

	/**
	 * Add a "join where" clause to the query.
	 *
	 * @param  string  $table
	 * @param  string  $first
	 * @param  string  $operator
	 * @param  string  $two
	 * @return \Hazzard\Database\Query
	 */
	public function leftJoinWhere($table, $one, $operator, $two)
	{
		return $this->joinWhere($table, $one, $operator, $two, 'left');
	}

	/**
	 * Add a basic where clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 *
	 * @throws \InvalidArgumentException
	 */
	public function where($column, $operator = null, $value = null, $boolean = 'and')
	{
		if (func_num_args() == 2) {
			list($value, $operator) = array($operator, '=');
		} elseif ($this->invalidOperatorAndValue($operator, $value)) {
			throw new \InvalidArgumentException("Value must be provided.");
		}

		if ($column instanceof Closure) {
			return $this->whereNested($column, $boolean);
		}

		if (!in_array(strtolower($operator), $this->operators, true)) {
			list($value, $operator) = array($operator, '=');
		}

		if ($value instanceof Closure) {
			return $this->whereSub($column, $operator, $value, $boolean);
		}

		if (is_null($value)) {
			return $this->whereNull($column, $boolean, $operator != '=');
		}

		$type = 'Basic';

		$this->wheres[] = compact('type', 'column', 'operator', 'value', 'boolean');

		if (!$value instanceof Expression) {
			$this->bindings[] = $value;
		}

		return $this;
	}

	/**
	 * Add an "or where" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  mixed   $value
	 * @return \Hazzard\Database\Query
	 */
	public function orWhere($column, $operator = null, $value = null)
	{
		return $this->where($column, $operator, $value, 'or');
	}

	/**
	 * Determine if the given operator and value combination is legal.
	 *
	 * @param  string  $operator
	 * @param  mixed  $value
	 * @return bool
	 */
	protected function invalidOperatorAndValue($operator, $value)
	{
		$isOperator = in_array($operator, $this->operators);

		return ($isOperator && $operator != '=' && is_null($value));
	}

	/**
	 * Add a raw where clause to the query.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function whereRaw($sql, array $bindings = array(), $boolean = 'and')
	{
		$type = 'raw';

		$this->wheres[] = compact('type', 'sql', 'boolean');

		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}

	/**
	 * Add a raw or where clause to the query.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereRaw($sql, array $bindings = array())
	{
		return $this->whereRaw($sql, $bindings, 'or');
	}

	/**
	 * Add a where between statement to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $boolean
	 * @param  bool  $not
	 * @return \Hazzard\Database\Query
	 */
	public function whereBetween($column, array $values, $boolean = 'and', $not = false)
	{
		$type = 'between';

		$this->wheres[] = compact('column', 'type', 'boolean', 'not');

		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Add an or where between statement to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereBetween($column, array $values)
	{
		return $this->whereBetween($column, $values, 'or');
	}

	/**
	 * Add a where not between statement to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function whereNotBetween($column, array $values, $boolean = 'and')
	{
		return $this->whereBetween($column, $values, $boolean, true);
	}

	/**
	 * Add an or where not between statement to the query.
	 *
	 * @param  string  $column
	 * @param  array   $values
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereNotBetween($column, array $values)
	{
		return $this->whereNotBetween($column, $values, 'or');
	}

	/**
	 * Add a nested where statement to the query.
	 *
	 * @param  \Closure $callback
	 * @param  string   $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function whereNested(Closure $callback, $boolean = 'and')
	{
		$query = $this->newQuery();

		$query->from($this->from);

		call_user_func($callback, $query);

		return $this->addNestedWhereQuery($query, $boolean);
	}

	/**
	 * Add another query builder as a nested where to the query builder.
	 *
	 * @param  \Hazzard\Database\Query $query
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function addNestedWhereQuery($query, $boolean = 'and')
	{
		if (count($query->wheres)) {
			$type = 'Nested';

			$this->wheres[] = compact('type', 'query', 'boolean');

			$this->mergeBindings($query);
		}

		return $this;
	}

	/**
	 * Add a full sub-select to the query.
	 *
	 * @param  string   $column
	 * @param  string   $operator
	 * @param  \Closure $callback
	 * @param  string   $boolean
	 * @return \Hazzard\Database\Query
	 */
	protected function whereSub($column, $operator, Closure $callback, $boolean)
	{
		$type = 'Sub';

		$query = $this->newQuery();

		call_user_func($callback, $query);

		$this->wheres[] = compact('type', 'column', 'operator', 'query', 'boolean');

		$this->mergeBindings($query);

		return $this;
	}

	/**
	 * Add a "where in" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $values
	 * @param  string  $boolean
	 * @param  bool    $not
	 * @return \Hazzard\Database\Query
	 */
	public function whereIn($column, $values, $boolean = 'and', $not = false)
	{
		$type = $not ? 'NotIn' : 'In';

		if ($values instanceof Closure) {
			return $this->whereInSub($column, $values, $boolean, $not);
		}

		$this->wheres[] = compact('type', 'column', 'values', 'boolean');

		$this->bindings = array_merge($this->bindings, $values);

		return $this;
	}

	/**
	 * Add an "or where in" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $values
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereIn($column, $values)
	{
		return $this->whereIn($column, $values, 'or');
	}

	/**
	 * Add a "where not in" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $values
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function whereNotIn($column, $values, $boolean = 'and')
	{
		return $this->whereIn($column, $values, $boolean, true);
	}

	/**
	 * Add an "or where not in" clause to the query.
	 *
	 * @param  string  $column
	 * @param  mixed   $values
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereNotIn($column, $values)
	{
		return $this->whereNotIn($column, $values, 'or');
	}

	/**
	 * Add a where in with a sub-select to the query.
	 *
	 * @param  string   $column
	 * @param  \Closure $callback
	 * @param  string   $boolean
	 * @param  bool     $not
	 * @return \Hazzard\Database\Query
	 */
	protected function whereInSub($column, Closure $callback, $boolean, $not)
	{
		$type = $not ? 'NotInSub' : 'InSub';

		call_user_func($callback, $query = $this->newQuery());

		$this->wheres[] = compact('type', 'column', 'query', 'boolean');

		$this->mergeBindings($query);

		return $this;
	}

	/**
	 * Add a "where null" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $boolean
	 * @param  bool    $not
	 * @return \Hazzard\Database\Query
	 */
	public function whereNull($column, $boolean = 'and', $not = false)
	{
		$type = $not ? 'NotNull' : 'Null';

		$this->wheres[] = compact('type', 'column', 'boolean');

		return $this;
	}

	/**
	 * Add an "or where null" clause to the query.
	 *
	 * @param  string  $column
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereNull($column)
	{
		return $this->whereNull($column, 'or');
	}

	/**
	 * Add a "where not null" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function whereNotNull($column, $boolean = 'and')
	{
		return $this->whereNull($column, $boolean, true);
	}

	/**
	 * Add an "or where not null" clause to the query.
	 *
	 * @param  string  $column
	 * @return \Hazzard\Database\Query
	 */
	public function orWhereNotNull($column)
	{
		return $this->whereNotNull($column, 'or');
	}

	/**
	 * Add a "having" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $operator
	 * @param  string  $value
	 * @return \Hazzard\Database\Query
	 */
	public function having($column, $operator = null, $value = null)
	{
		$type = 'basic';

		$this->havings[] = compact('type', 'column', 'operator', 'value');

		$this->bindings[] = $value;

		return $this;
	}
	/**
	 * Add a raw having clause to the query.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @param  string  $boolean
	 * @return \Hazzard\Database\Query
	 */
	public function havingRaw($sql, array $bindings = array(), $boolean = 'and')
	{
		$type = 'raw';

		$this->havings[] = compact('type', 'sql', 'boolean');

		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}
	/**
	 * Add a raw or having clause to the query.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return \Hazzard\Database\Query
	 */
	public function orHavingRaw($sql, array $bindings = array())
	{
		return $this->havingRaw($sql, $bindings, 'or');
	}

	/**
	 * Add an "order by" clause to the query.
	 *
	 * @param  string  $column
	 * @param  string  $direction
	 * @return \Hazzard\Database\Query
	 */
	public function orderBy($column, $direction = 'asc')
	{
		$direction = strtolower($direction) == 'asc' ? 'asc' : 'desc';

		$this->orders[] = compact('column', 'direction');

		return $this;
	}

	/**
	 * Add a raw "order by" clause to the query.
	 *
	 * @param  string  $sql
	 * @param  array  $bindings
	 * @return \Hazzard\Database\Query
	 */
	public function orderByRaw($sql, $bindings = array())
	{
		$type = 'raw';

		$this->orders[] = compact('type', 'sql');

		$this->bindings = array_merge($this->bindings, $bindings);

		return $this;
	}

	/**
	 * Set the "offset" value of the query.
	 *
	 * @param  int  $value
	 * @return \Hazzard\Database\Query
	 */
	public function offset($value)
	{
		$this->offset = max(0, $value);

		return $this;
	}

	/**
	 * Alias to set the "offset" value of the query.
	 *
	 * @param  int  $value
	 * @return \Hazzard\Database\Query
	 */
	public function skip($value)
	{
		return $this->offset($value);
	}

	/**
	 * Set the "limit" value of the query.
	 *
	 * @param  int  $value
	 * @return \Hazzard\Database\Query
	 */
	public function limit($value)
	{
		if ($value > 0) $this->limit = $value;

		return $this;
	}

	/**
	 * Alias to set the "limit" value of the query.
	 *
	 * @param  int  $value
	 * @return \Hazzard\Database\Query
	 */
	public function take($value)
	{
		return $this->limit($value);
	}

	/**
	 * Execute a query for a single record by ID.
	 *
	 * @param  int    $id
	 * @param  array  $columns
	 * @return mixed
	 */
	public function find($id, $columns = array('*'))
	{
		$uniqueIdentifier = isset($this->model) ? $this->model->getKeyName() : 'id';

		return $this->where($uniqueIdentifier, '=', $id)->first($columns);
	}

	/**
	 * Pluck a single column's value from the first result of a query.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function pluck($column)
	{
		$result = $this->first(array($column));

		if (!is_null($result)) {
			if (isset($this->model)) {
				$result = $result->getAttributes();
			}

			$result = (array) $result;
		}

		return count($result) > 0 ? reset($result) : null;
	}

	/**
	 * Execute the query and get the first result.
	 *
	 * @param  array   $columns
	 * @return mixed
	 */
	public function first($columns = array('*'))
	{
		$results = $this->take(1)->get($columns);

		return count($results) > 0 ? reset($results) : null;
	}

	/**
	 * Execute the query as a "select" statement.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public function get($columns = array('*'))
	{
		if (is_null($this->columns)) $this->columns = $columns;

		$results = $this->runSelect();

		if (isset($this->model)) {
			return $this->getModels($results);
		}

		return $results;
	}

	/**
	 * Convert results to model instances.
	 *
	 * @param  array  $results
	 * @return array
	 */
	public function getModels(array $results)
	{
		$models = array();

		foreach ($results as $result) {
			$models[] = $this->model->newModel($result);
		}

		return $models;
	}

	/**
	 * Run the query as a "select" statement against the connection.
	 *
	 * @return array
	 */
	protected function runSelect()
	{
		return $this->db->select($this->toSql(), $this->bindings);
	}

	/**
	 * Get the SQL representation of the query.
	 *
	 * @return string
	 */
	public function toSql()
	{
		return $this->compileSelect($this);
	}

	/**
	 * Determine if any rows exist for the current query.
	 *
	 * @return bool
	 */
	public function exists()
	{
		return $this->count() > 0;
	}

	/**
	 * Retrieve the "count" result of the query.
	 *
	 * @param  string  $column
	 * @return int
	 */
	public function count($column = '*')
	{
		return (int) $this->aggregate(__FUNCTION__, array($column));
	}

	/**
	 * Retrieve the minimum value of a given column.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function min($column)
	{
		return $this->aggregate(__FUNCTION__, array($column));
	}

	/**
	 * Retrieve the maximum value of a given column.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function max($column)
	{
		return $this->aggregate(__FUNCTION__, array($column));
	}

	/**
	 * Retrieve the sum of the values of a given column.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function sum($column)
	{
		return $this->aggregate(__FUNCTION__, array($column));
	}

	/**
	 * Retrieve the average of the values of a given column.
	 *
	 * @param  string  $column
	 * @return mixed
	 */
	public function avg($column)
	{
		return $this->aggregate(__FUNCTION__, array($column));
	}

	/**
	 * Execute an aggregate function on the database.
	 *
	 * @param  string  $function
	 * @param  array   $columns
	 * @return mixed
	 */
	public function aggregate($function, $columns = array('*'))
	{
		$this->aggregate = compact('function', 'columns');

		$results = $this->get($columns);

		$this->columns = null; 

		$this->aggregate = null;

		if (isset($results[0])) {
			$result = $results[0];

			if (isset($this->model)) {
				$result = $result->getAttributes();
			}

			$result = (array) $result;

			return $result['aggregate'];
		}
	}

	/**
	 * Insert a new record into the database.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function insert(array $values)
	{
		if (!is_array(reset($values))) {
			$values = array($values);
		} else {
			foreach ($values as $key => $value) {
				ksort($value); $values[$key] = $value;
			}
		}

		$bindings = array();

		foreach ($values as $record) {
			$bindings = array_merge($bindings, array_values($record));
		}

		$sql = $this->compileInsert($values);

		$bindings = $this->cleanBindings($bindings);

		return $this->db->insert($sql, $bindings);
	}

	/**
	 * Replace a new record into the database.
	 *
	 * @param  array  $values
	 * @return bool
	 */
	public function replace(array $values)
	{
		if (!is_array(reset($values))) {
			$values = array($values);
		} else {
			foreach ($values as $key => $value) {
				ksort($value); $values[$key] = $value;
			}
		}

		$bindings = array();

		foreach ($values as $record) {
			$bindings = array_merge($bindings, array_values($record));
		}

		$sql = $this->compileReplace($values);

		$bindings = $this->cleanBindings($bindings);

		return $this->db->insert($sql, $bindings);
	}

	/**
	 * Insert a new record and get the value of the primary key.
	 *
	 * @param  array   $values
	 * @return int
	 */
	public function insertGetId(array $values)
	{
		$sql = $this->compileInsert($values);

		$values = $this->cleanBindings($values);

		$this->db->insert($sql, $values);

		$id = $this->db->getPdo()->lastInsertId();

		return is_numeric($id) ? (int) $id : $id;
	}

	/**
	 * Update a record in the database.
	 *
	 * @param  array  $values
	 * @return int
	 */
	public function update(array $values)
	{
		$bindings = array_values(array_merge($values, $this->bindings));

		$sql = $this->compileUpdate($values);

		return $this->db->update($sql, $this->cleanBindings($bindings));
	}

	/**
	 * Delete a record from the database.
	 *
	 * @param  mixed  $id
	 * @return int
	 */
	public function delete($id = null)
	{
		$uniqueIdentifier = isset($this->model) ? $this->model->getKeyName() : 'id';

		if (!is_null($id)) $this->where($uniqueIdentifier, '=', $id);

		$sql = $this->compileDelete();

		return $this->db->delete($sql, $this->bindings);
	}

	/**
	 * Run a truncate statement on the table.
	 *
	 * @return void
	 */
	public function truncate()
	{
		foreach ($this->compileTruncate() as $sql => $bindings) {
			$this->db->statement($sql, $bindings);
		}
	}

	/**
	 * Add a "group by" clause to the query.
	 *
	 * @param  dynamic $columns
	 * @return \Hazzard\Database\Query
	 */
	public function groupBy()
	{
		$this->groups = array_merge((array) $this->groups, func_get_args());

		return $this;
	}

	/**
	 * Get a new instance of the query builder.
	 *
	 * @return \Hazzard\Database\Query
	 */
	public function newQuery()
	{
		return new Query($this->db);
	}

	/**
	 * Merge an array of bindings into our bindings.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return \Hazzard\Database\Query
	 */
	public function mergeBindings(Query $query)
	{
		$this->bindings = array_values(array_merge($this->bindings, $query->bindings));

		return $this;
	}

	/**
	 * Remove all of the expressions from a list of bindings.
	 *
	 * @param  array  $bindings
	 * @return array
	 */
	protected function cleanBindings(array $bindings)
	{
		return array_values(array_filter($bindings, function($binding) {
			return !$binding instanceof Expression;
		}));
	}

	/**
	 * Create a raw database expression.
	 *
	 * @param  mixed $value
	 * @return \Hazzard\Database\Expression
	 */
	public function raw($value)
	{
		return $this->connection->raw($value);
	}

	/**
	 * Add a binding to the query.
	 *
	 * @param  mixed  $value
	 * @return \Hazzard\Database\Query
	 */
	public function addBinding($value)
	{
		$this->bindings[] = $value;

		return $this;
	}

	/**
	 * Set a model instance for the model being queried.
	 *
	 * @param  \Hazzard\Database\Model|null  $model
	 * @return \Hazzard\Database\Query
	 */
	public function setModel($model)
	{
		$this->model = $model;

		return $this;
	}

	/**
	 * Compile a select query into SQL.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return string
	 */
	public function compileSelect(Query $query)
	{
		if (is_null($query->columns)) $query->columns = array('*');

		return trim($this->concatenate($this->compileComponents($query)));
	}

	/**
	 * Compile the components necessary for a select clause.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return array
	 */
	protected function compileComponents(Query $query)
	{
		$sql = array();

		$selectComponents = array(
			'aggregate',
			'columns',
			'from',
			'joins',
			'wheres',
			'groups',
			'havings',
			'orders',
			'limit',
			'offset'
		);

		foreach ($selectComponents as $component) {
			if (!is_null($query->$component)) {
				$method = 'compile'.ucfirst($component);

				$sql[$component] = $this->$method($query, $query->$component);
			}
		}

		return $sql;
	}

	/**
	 * Compile an aggregated select clause.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $aggregate
	 * @return string
	 */
	protected function compileAggregate(Query $query, $aggregate)
	{
		$column = $this->columnize($aggregate['columns']);

		if ($query->distinct && $column !== '*') {
			$column = 'distinct '.$column;
		}

		return 'select '.$aggregate['function'].'('.$column.') as aggregate';
	}

	/**
	 * Compile the "select *" portion of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $columns
	 * @return string
	 */
	protected function compileColumns(Query $query, $columns)
	{
		if (!is_null($query->aggregate)) return;

		$select = $query->distinct ? 'select distinct ' : 'select ';

		return $select.$this->columnize($columns);
	}

	/**
	 * Compile the "from" portion of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  string  $table
	 * @return string
	 */
	protected function compileFrom(Query $query, $table)
	{
		return 'from '.$this->wrapTable($table);
	}

	/**
	 * Compile the "join" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $joins
	 * @return string
	 */
	protected function compileJoins(Query $query, $joins)
	{
		$sql = array();

		foreach ($joins as $join) {
			$table = $this->wrapTable($join->table);

			$clauses = array();

			foreach ($join->clauses as $clause) {
				$clauses[] = $this->compileJoinConstraint($clause);
			}

			$clauses[0] = $this->removeLeadingBoolean($clauses[0]);

			$clauses = implode(' ', $clauses);

			$type = $join->type;

			$sql[] = "$type join $table on $clauses";
		}

		return implode(' ', $sql);
	}

	/**
	 * Create a join clause constraint segment.
	 *
	 * @param  array   $clause
	 * @return string
	 */
	protected function compileJoinConstraint(array $clause)
	{
		$first = $this->wrap($clause['first']);

		$second = $clause['where'] ? '?' : $this->wrap($clause['second']);

		return "{$clause['boolean']} $first {$clause['operator']} $second";
	}

	/**
	 * Compile the "where" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return string
	 */
	protected function compileWheres(Query $query)
	{
		$sql = array();

		if (is_null($query->wheres)) return '';

		foreach ($query->wheres as $where) {
			$method = "compileWhere{$where['type']}";

			$sql[] = $where['boolean'].' '.$this->$method($where);
		}

		if (count($sql) > 0) {
			$sql = implode(' ', $sql);

			return 'where '.preg_replace('/and |or /', '', $sql, 1);
		}

		return '';
	}

	/**
	 * Compile a nested where clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereNested($where)
	{
		$nested = $where['query'];

		return '('.substr($this->compileWheres($nested), 6).')';
	}

	/**
	 * Compile a where condition with a sub-select.
	 *
	 * @param  array   $where
	 * @return string
	 */
	protected function compileWhereSub($where)
	{
		$select = $this->compileSelect($where['query']);

		return $this->wrap($where['column']).' '.$where['operator']." ($select)";
	}

	/**
	 * Compile a basic where clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereBasic($where)
	{
		$value = $this->parameter($where['value']);

		return $this->wrap($where['column']).' '.$where['operator'].' '.$value;
	}

	/**
	 * Compile a "between" where clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereBetween($where)
	{
		$between = $where['not'] ? 'not between' : 'between';

		return $this->wrap($where['column']).' '.$between.' ? and ?';
	}

	/**
	 * Compile a "where in" clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereIn($where)
	{
		$values = $this->parameterize($where['values']);

		return $this->wrap($where['column']).' in ('.$values.')';
	}

	/**
	 * Compile a "where not in" clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereNotIn($where)
	{
		$values = $this->parameterize($where['values']);

		return $this->wrap($where['column']).' not in ('.$values.')';
	}

	/**
	 * Compile a where in sub-select clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereInSub($where)
	{
		$select = $this->compileSelect($where['query']);

		return $this->wrap($where['column']).' in ('.$select.')';
	}

	/**
	 * Compile a where not in sub-select clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereNotInSub($where)
	{
		$select = $this->compileSelect($where['query']);

		return $this->wrap($where['column']).' not in ('.$select.')';
	}

	/**
	 * Compile a "where null" clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereNull($where)
	{
		return $this->wrap($where['column']).' is null';
	}

	/**
	 * Compile a "where not null" clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereNotNull($where)
	{
		return $this->wrap($where['column']).' is not null';
	}

	/**
	 * Compile a raw where clause.
	 *
	 * @param  array  $where
	 * @return string
	 */
	protected function compileWhereRaw($where)
	{
		return $where['sql'];
	}

	/**
	 * Compile the "group by" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $groups
	 * @return string
	 */
	protected function compileGroups(Query $query, $groups)
	{
		return 'group by '.$this->columnize($groups);
	}

	/**
	 * Compile the "having" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $havings
	 * @return string
	 */
	protected function compileHavings(Query $query, $havings)
	{
		$sql = implode(' ', array_map(array($this, 'compileHaving'), $havings));
		
		return 'having '.preg_replace('/and /', '', $sql, 1);
	}

	/**
	 * Compile a single having clause.
	 *
	 * @param  array   $having
	 * @return string
	 */
	protected function compileHaving(array $having)
	{
		if ($having['type'] === 'raw') {
			return $having['boolean'].' '.$having['sql'];
		}

		return $this->compileBasicHaving($having);
	}

	/**
	 * Compile a basic having clause.
	 *
	 * @param  array   $having
	 * @return string
	 */
	protected function compileBasicHaving($having)
	{
		$column = $this->wrap($having['column']);

		$parameter = $this->parameter($having['value']);

		return 'and '.$column.' '.$having['operator'].' '.$parameter;
	}

	/**
	 * Compile the "order by" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  array  $orders
	 * @return string
	 */
	protected function compileOrders(Query $query, $orders)
	{
		$me = $this;

		return 'order by '.implode(', ', array_map(function($order) use ($me) {
			if (isset($order['sql'])) return $order['sql'];
			
			if (strtolower($order['column']) === 'rand()') {
				return $order['column'];
			}

			return $me->wrap($order['column']).' '.$order['direction'];
		}, $orders));
	}

	/**
	 * Compile the "limit" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  int  $limit
	 * @return string
	 */
	protected function compileLimit(Query $query, $limit)
	{
		return 'limit '.(int) $limit;
	}

	/**
	 * Compile the "offset" portions of the query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @param  int  $offset
	 * @return string
	 */
	protected function compileOffset(Query $query, $offset)
	{
		return 'offset '.(int) $offset;
	}

	/**
	 * Compile an insert statement into SQL.
	 *
	 * @param  array  $values
	 * @return string
	 */
	public function compileInsert(array $values)
	{
		$table = $this->wrapTable($this->from);

		if (!is_array(reset($values))) {
			$values = array($values);
		}

		$columns = $this->columnize(array_keys(reset($values)));

		$parameters = $this->parameterize(reset($values));

		$value = array_fill(0, count($values), "($parameters)");

		$parameters = implode(', ', $value);

		return "insert into $table ($columns) values $parameters";
	}

	/**
	 * Compile an replace statement into SQL.
	 *
	 * @param  array  $values
	 * @return string
	 */
	public function compileReplace(array $values)
	{
		$table = $this->wrapTable($this->from);

		if (!is_array(reset($values))) {
			$values = array($values);
		}

		$columns = $this->columnize(array_keys(reset($values)));

		$parameters = $this->parameterize(reset($values));

		$value = array_fill(0, count($values), "($parameters)");

		$parameters = implode(', ', $value);

		return "replace into $table ($columns) values $parameters";
	}

	/**
	 * Compile an update statement into SQL.
	 *
	 * @param  array  $values
	 * @return string
	 */
	public function compileUpdate($values)
	{
		$table = $this->wrapTable($this->from);

		$columns = array();

		foreach ($values as $key => $value) {
			$columns[] = $this->wrap($key).' = '.$this->parameter($value);
		}

		$columns = implode(', ', $columns);

		if (isset($this->joins)) {
			$joins = ' '.$this->compileJoins($this, $this->joins);
		} else {
			$joins = '';
		}

		$where = $this->compileWheres($this);

		$sql = trim("update {$table}{$joins} set $columns $where");

		if (isset($this->orders)) {
			$sql .= ' '.$this->compileOrders($this, $this->orders);
		}

		if (isset($this->limit)) {
			$sql .= ' '.$this->compileLimit($this, $this->limit);
		}

		return rtrim($sql);
	}

	/**
	 * Compile a delete statement into SQL.
	 *
	 * @return string
	 */
	public function compileDelete()
	{
		$table = $this->wrapTable($this->from);

		$where = is_array($this->wheres) ? $this->compileWheres($this) : '';

		$sql = trim("delete from $table ".$where);

		if (isset($this->limit)) {
			$sql .= ' '.$this->compileLimit($this, $this->limit);
		}

		return rtrim($sql);
	}

	/**
	 * Compile a truncate table statement into SQL.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return array
	 */
	public function compileTruncate()
	{
		return array('truncate '.$this->wrapTable($this->from) => array());
	}

	/**
	 * Wrap a table in keyword identifiers.
	 *
	 * @param  string  $table
	 * @return string
	 */
	public function wrapTable($table)
	{
		if ($this->isExpression($table)) {
			return $this->getValue($table);
		}

		return $this->wrap($this->db->getTablePrefix().$table);
	}

	/**
	 * Wrap a value in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function wrap($value)
	{
		if ($this->isExpression($value)) {
			return $this->getValue($value);
		}

		if (strpos(strtolower($value), ' as ') !== false) {
			$segments = explode(' ', $value);

			return $this->wrap($segments[0]).' as '.$this->wrap($segments[2]);
		}

		$wrapped = array();

		$segments = explode('.', $value);

		foreach ($segments as $key => $segment) {
			if ($key == 0 && count($segments) > 1) {
				$wrapped[] = $this->wrapTable($segment);
			} else {
				$wrapped[] = $this->wrapValue($segment);
			}
		}

		return implode('.', $wrapped);
	}

	/**
	 * Wrap a single string in keyword identifiers.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function wrapValue($value)
	{
		return $value !== '*' ? sprintf($this->wrapper, $value) : $value;
	}

	/**
	 * Concatenate an array of segments, removing empties.
	 *
	 * @param  array   $segments
	 * @return string
	 */
	protected function concatenate($segments)
	{
		return implode(' ', array_filter($segments, function($value) {
			return (string) $value !== '';
		}));
	}

	/**
	 * Remove the leading boolean from a statement.
	 *
	 * @param  string  $value
	 * @return string
	 */
	protected function removeLeadingBoolean($value)
	{
		return preg_replace('/and |or /', '', $value, 1);
	}

	/**
	 * Create query parameter place-holders for an array.
	 *
	 * @param  array   $values
	 * @return string
	 */
	public function parameterize(array $values)
	{
		return implode(', ', array_map(array($this, 'parameter'), $values));
	}

	/**
	 * Get the appropriate query parameter place-holder for a value.
	 *
	 * @param  mixed   $value
	 * @return string
	 */
	public function parameter($value)
	{
		return $this->isExpression($value) ? $this->getValue($value) : '?';
	}

	/**
	 * Convert an array of column names into a delimited string.
	 *
	 * @param  array   $columns
	 * @return string
	 */
	public function columnize(array $columns)
	{
		return implode(', ', array_map(array($this, 'wrap'), $columns));
	}

	/**
	 * Get the value of a raw expression.
	 *
	 * @param  \Hazzard\Database\Query $expression
	 * @return string
	 */
	public function getValue($expression)
	{
		return $expression->getValue();
	}

	/**
	 * Determine if the given value is a raw expression.
	 *
	 * @param  mixed $value
	 * @return bool
	 */
	public function isExpression($value)
	{
		return $value instanceof Expression;
	}
}
