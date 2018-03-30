<?php
final class Category {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function get($id) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	category_id as id, " .
				"	name, " .
				"	description " .
				"from " .
				"	category " .
				"where " .
				"	category_id = {$id}";
		
		$dbq = $database->query($sql, array(0, 1));
		
		if($database->getNumRows($dbq) > 0)
			return $database->getArray($dbq);
		
		return null;
	}
	
	public function getCollection() {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	category_id as id, " .
				"	name " .
				"from " .
				"	category " .
				"order by " .
				"	name";
				
		$dbq = $database->query($sql);
		
		$data = array();
	
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
	
		return $data;
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(category_id), 0) + 1 from category');
		
		$sql = "insert into category (" .
				"	category_id, " .
				"	name, " .
				"	description, " .
				"	status, " .
				"	sort_order, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->name}', " .
				"	'{$data->description}', " .
				"	{$data->status}, " .
				"	{$id}, " .
				"	now(), " .
				"	now()" .
				")";
		
		return $database->query($sql) ? (int) $id : 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	category " .
				"set " .
				"	name = '{$data->name}', " .
				"	description = '{$data->description}', " .
				"	status = {$data->status}, " .
				"	date_modified = now() " .
				"where " .
				"	category_id = {$data->id}";
		
		return $database->query($sql);
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from category where category_id = {$id}";
		
		return $database->query($sql);
	}
	
}
?>