<?php namespace Hazzard\Database;

class Model implements \ArrayAccess {

	/**
	 * The database connection instance.
	 *
	 * @var \Hazzard\Database\Connection
	 */
	protected static $db;

	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table;

	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';

	/**
	 * The model's attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The model attribute's original state.
	 *
	 * @var array
	 */
	protected $original = array();

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = array();

	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = array('*');

	/**
	 * Indicates if all mass assignment is enabled.
	 *
	 * @var bool
	 */
	protected static $unguarded = true;

	/**
	 * Indicates if the model exists.
	 *
	 * @var bool
	 */
	public $exists = false;
	
	/**
	 * Create a new Model instance.
	 *
	 * @param  array  $attributes
	 * @return void
	 */
	public function __construct(array $attributes = array())
	{
		$this->syncOriginal();

		$this->fill($attributes);
	}

	/**
	 * Get all of the models from the database.
	 *
	 * @param  array  $columns
	 * @return array
	 */
	public static function all($columns = array('*'))
	{
		$instance = new static;

		return $instance->newQuery()->get($columns);
	}

	/**
	 * Find a model by its primary key.
	 *
	 * @param  mixed  $id
	 * @param  array  $columns
	 * @return Model
	 */
	public static function find($id, $columns = array('*'))
	{
		$instance = new static;
		
		return $instance->newQuery()->where($instance->getKeyName(), $id)->first($columns);
	}

	/**
	 * Set a given attribute on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function setAttribute($key, $value)
	{
		if ($this->hasSetMutator($key)) {
			$method = 'set'.studly_case($key).'Attribute';

			return $this->{$method}($value);
		}

		$this->attributes[$key] = $value;
	}

	/**
	 * Get an attribute from the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function getAttribute($key)
	{
		$inAttributes = array_key_exists($key, $this->attributes);

		if ($inAttributes || $this->hasGetMutator($key)) {
			return $this->getAttributeValue($key);
		}
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$query = $this->newQuery();

		if ($this->exists) {
			$saved = $this->performUpdate($query);
		} else {
			$saved = $this->performInsert($query);
		}

		if ($saved) $this->syncOriginal();

		return $saved;
	}

	/**
	 * Perform a model update operation.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return bool
	 */
	protected function performUpdate(Query $query)
	{
		$dirty = $this->getDirty();

		if (count($dirty) > 0) {
			$this->setKeysForSaveQuery($query)->update($dirty);
		}

		return true;
	}

	/**
	 * Perform a model insert operation.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return bool
	 */
	protected function performInsert(Query $query)
	{
		$attributes = $this->attributes;
		
		$keyName = $this->getKeyName();

		$id = $query->insertGetId($attributes);

		$this->setAttribute($keyName, $id);

		$this->exists = true;

		return true;
	}

	/**
	 * Delete the model from the database.
	 *
	 * @return bool|null
	 */
	public function delete()
	{
		if ($this->exists) {
			$query = $this->newQuery()->where($this->getKeyName(), $this->getKey());
			
			$query->delete();

			$this->exists = false;

			return true;
		}
	}

	/**
	 * Set the keys for a save update query.
	 *
	 * @param  \Hazzard\Database\Query  $query
	 * @return \Hazzard\Database\Query
	 */
	protected function setKeysForSaveQuery(Query $query)
	{
		$query->where($this->getKeyName(), $this->getKeyForSaveQuery());

		return $query;
	}

	/**
	 * Get the primary key value for a save query.
	 *
	 * @return mixed
	 */
	protected function getKeyForSaveQuery()
	{
		if (isset($this->original[$this->getKeyName()])) {
			return $this->original[$this->getKeyName()];
		} else {
			return $this->getAttribute($this->getKeyName());
		}
	}

	/**
	 * Get the primary key for the model.
	 *
	 * @return string
	 */
	public function getKeyName()
	{
		return $this->primaryKey;
	}

	/**
	 * Get the value of the model's primary key.
	 *
	 * @return mixed
	 */
	public function getKey()
	{
		return $this->getAttribute($this->getKeyName());
	}

	/**
	 * Get the attributes that have been changed since last sync.
	 *
	 * @return array
	 */
	public function getDirty()
	{
		$dirty = array();

		foreach ($this->attributes as $key => $value) {
			if (!array_key_exists($key, $this->original) || $value !== $this->original[$key]) {
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Get a plain attribute.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function getAttributeValue($key)
	{
		$value = $this->getAttributeFromArray($key);

		if ($this->hasGetMutator($key)) {
			return $this->mutateAttribute($key, $value);
		}

		return $value;
	}

	/**
	 * Get an attribute from the $attributes array.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	protected function getAttributeFromArray($key)
	{
		if (array_key_exists($key, $this->attributes)) {
			return $this->attributes[$key];
		}
	}

	/**
	 * Get the value of an attribute using its mutator.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return mixed
	 */
	protected function mutateAttribute($key, $value)
	{
		return $this->{'get'.studly_case($key).'Attribute'}($value);
	}

	/**
	 * Determine if a set mutator exists for an attribute.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasSetMutator($key)
	{
		return method_exists($this, 'set'.studly_case($key).'Attribute');
	}

	/**
	 * Determine if a get mutator exists for an attribute.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function hasGetMutator($key)
	{
		return method_exists($this, 'get'.studly_case($key).'Attribute');
	}

	/**
	 * Sync the original attributes with the current.
	 *
	 * @return Model
	 */
	public function syncOriginal()
	{
		$this->original = $this->attributes;

		return $this;
	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @return Model
	 */
	public function fill(array $attributes)
	{
		foreach ($this->fillableFromArray($attributes) as $key => $value) {
			if ($this->isFillable($key)) {
				$this->setAttribute($key, $value);
			}
		}

		return $this;
	}

	/**
	 * Get the fillable attributes of a given array.
	 *
	 * @param  array  $attributes
	 * @return array
	 */
	protected function fillableFromArray(array $attributes)
	{
		if (count($this->fillable) > 0 && ! static::$unguarded) {
			return array_intersect_key($attributes, array_flip($this->fillable));
		}

		return $attributes;
	}

	/**
	 * Determine if the given attribute may be mass assigned.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function isFillable($key)
	{
		if (static::$unguarded) return true;

		if (in_array($key, $this->fillable)) return true;

		if ($this->isGuarded($key)) return false;

		return empty($this->fillable) && ! starts_with($key, '_');
	}

	/**
	 * Determine if the given key is guarded.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function isGuarded($key)
	{
		return in_array($key, $this->guarded) || $this->guarded == array('*');
	}

	/**
	 * Get a new query for the model's table.
	 *
	 * @return \Hazzard\Database\Query
	 */
	public function newQuery()
	{
		return with(static::getConnection())
				->table($this->table)
				->setModel($this);
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool   $exists
	 * @return Model
	 */
	public function newInstance($attributes = array(), $exists = false)
	{
		$model = new static((array) $attributes);

		$model->exists = $exists;

		return $model;
	}

	/**
	 * Create a new model instance that is existing.
	 *
	 * @param  array  $attributes
	 * @return Model
	 */
	public function newModel($attributes = array())
	{
		$instance = $this->newInstance(array(), true);

		$instance->setRawAttributes((array) $attributes, true);

		return $instance;
	}

	/**
	 * Set the array of model attributes. No checking is done.
	 *
	 * @param  array  $attributes
	 * @param  bool   $sync
	 * @return void
	 */
	public function setRawAttributes(array $attributes, $sync = false)
	{
		$this->attributes = $attributes;

		if ($sync) $this->syncOriginal();
	}

	public static function getTable()
	{
		$instance = new static;
		return $instance->table;
	}

	/**
	 * Get the database connection instance.
	 *
	 * @return \Hazzard\Database\Connection
	 */
	public static function getConnection()
	{
		return static::$db;
	}

	/**
	 * Set the database connection instance.
	 *
	 * @param  \Hazzard\Database\Connection  $connection
	 * @return void
	 */
	public static function setConnection(Connection $db)
	{
		static::$db = $db;
	}

	/**
	 * Convert the model instance to JSON.
	 *
	 * @param  int  $options
	 * @return string
	 */
	public function toJson($options = 0)
	{
		return json_encode($this->toArray(), $options);
	}

	/**
	 * Convert the model instance to an array.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$attributes = array();
		
		foreach ($this->getAttributes() as $key => $value) {
			$attributes[$key] = $this->getAttribute($key);
		}

		return $attributes;
	}

	/**
	 * Get all of the current attributes on the model.
	 *
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * Determine if the given attribute exists.
	 *
	 * @param  mixed  $offset
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return isset($this->$offset);
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		return $this->$offset;
	}

	/**
	 * Set the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @param  mixed  $value
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->$offset = $value;
	}

	/**
	 * Unset the value for a given offset.
	 *
	 * @param  mixed  $offset
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->$offset);
	}

	/**
	 * Dynamically retrieve attributes on the model.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->getAttribute($key);
	}

	/**
	 * Dynamically set attributes on the model.
	 *
	 * @param  string  $key
	 * @param  mixed   $value
	 * @return void
	 */
	public function __set($key, $value)
	{
		$this->setAttribute($key, $value);
	}

	/**
	 * Determine if an attribute exists on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __isset($key)
	{
		return (isset($this->attributes[$key]) || ($this->hasGetMutator($key) && ! is_null($this->getAttributeValue($key))));
	}

	/**
	 * Unset an attribute on the model.
	 *
	 * @param  string  $key
	 * @return void
	 */
	public function __unset($key)
	{
		unset($this->attributes[$key]);
	}

	/**
	 * Handle dynamic method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		$query = $this->newQuery();
		return call_user_func_array(array($query, $method), $parameters);
	}

	/**
	 * Handle dynamic static method calls into the method.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public static function __callStatic($method, $parameters)
	{
		$instance = new static;
		return call_user_func_array(array($instance, $method), $parameters);
	}
}