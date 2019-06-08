<?php namespace Hazzard\Support;

use Hazzard\Database\Query;

class DataTables {

	protected $request;

	protected $columns;

	protected $query;

	function __construct(array $request, array $columns, Query $query)
	{
		$this->request = $request;
		
		$this->columns = $columns;
		
		$this->query = $query;
		
		$this->query->setModel(null);
	}

	public function get($countColumn = null)
	{
		if (is_null($countColumn)) {
			$countColumn = 'users.id';
		}

		$originalQuery = clone $this->query;

		$this->setSelectColumns();

		$this->filter();

		$this->order();

		$countQuery = clone $this->query;

		$this->limit();

		$data = $this->query->get();

		return array(
			'data' => $this->formatData($data),
			'draw' => intval($this->request['draw']),
			'recordsTotal' => intval($originalQuery->count($countColumn)),
			'recordsFiltered' => intval($countQuery->count($countColumn))
		);
	}

	protected function setSelectColumns()
	{
		$columns = array();
		
		foreach ($this->columns as $column) {
			$columns[] = $column['db'] . (isset($column['as']) ? " as {$column['as']}" : '');
		}

		$this->query->select($columns);
	}

	protected function filter()
	{
		$columnSearch = array();
		
		$dtColumns = $this->pluck($this->columns, 'dt');

		if (isset($this->request['search']) && $this->request['search']['value'] != '') {
			$searchValue = trim($this->request['search']['value']);

			$request = $this->request;
			$columns = $this->columns;

			$this->query->whereNested(function($query) use($request, $columns, $dtColumns, $searchValue) {
				
				for ($i = 0, $ien = count($request['columns']); $i < $ien; $i++ ) {
					$requestColumn = $request['columns'][$i];
					
					$columnIdx = array_search($requestColumn['data'], $dtColumns);
					
					$column = $columns[$columnIdx];

					if ($requestColumn['searchable'] == 'true' && strlen($searchValue) > 0) {
						$query->orWhere($column['db'], 'LIKE', '%'.$searchValue.'%');
					}
				}
			});
		}

		for ($i = 0, $ien = count($this->request['columns']); $i < $ien ; $i++) {
			$requestColumn = $this->request['columns'][$i];
			
			$columnIdx = array_search($requestColumn['data'], $dtColumns);
			
			$column = $this->columns[$columnIdx];

			$searchValue = trim($requestColumn['search']['value']);

			if ($requestColumn['searchable'] == 'true' && strlen($searchValue) > 0) {
				$this->query->where($column['db'], 'LIKE', '%'.$searchValue.'%');
			}
		}
	}

	protected function order()
	{
		if (!isset($this->request['order']) || !count($this->request['order'])) return;

		$dtColumns = $this->pluck($this->columns, 'dt');

		for ($i = 0, $ien = count($this->request['order']); $i < $ien; $i++) {
			$columnIdx = intval($this->request['order'][$i]['column']);
			
			$requestColumn = $this->request['columns'][$columnIdx];

			$columnIdx = array_search($requestColumn['data'], $dtColumns);
			
			$column = $this->columns[$columnIdx];

			if ($requestColumn['orderable'] == 'true') {
				$dir = $this->request['order'][$i]['dir'] === 'asc' ? 'ASC' : 'DESC';
				
				$this->query->orderBy($column['db'], $dir);
			}
		}
	}

	protected function limit()
	{
		if (!isset($this->request['start']) || $this->request['length'] == -1) return;
		
		$this->query->take($this->request['length']);
			
		$this->query->skip($this->request['start']);
	}

	protected function formatData($results)
	{
		$data = array();
		
		if (count($results)) {
			foreach ($results as $result) {
				$record = array();
				
				foreach ($this->columns as $column) {
					$value = isset($column['as']) ? $result->{$column['as']} : $result->{$column['db']};
					
					if (isset($column['formatter'])) {
						$record[$column['dt']] = call_user_func($column['formatter'], $value, $result);
					} else {
						$record[$column['dt']] = $value;
					}
				}
				$data[] = $record;
			}
		}

		return $data;
	}

	protected function pluck($array, $prop) 
	{
		$out = array();
		
		for ($i = 0, $len = count($array); $i < $len; $i++) {
			$out[] = $array[$i][$prop];
		}
		
		return $out;
	}
}
