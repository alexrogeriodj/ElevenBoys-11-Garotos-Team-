<?php
final class Architect {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function get($id) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	a.architect_id as id, " .
				"	a.name, " .
				"	a.email, " .
				"	a.description, " .
				"	a.phone, " .
				"	a.cellphone, " .
				"	b.image_id as image_id, " .
				"	b.name as image_name " .
				"from " .
				"	architect a " .
				"	inner join image b on a.image_id = b.image_id " .
				"where " .
				"	a.architect_id = {$id}";
		
		$dbq = $database->query($sql, array(0, 1));
		
		if($database->getNumRows($dbq) > 0)
			return $database->getArray($dbq);
		
		return null;
	}
	
	public function getCollectionSql() {
		$sql = "select " .
				"	architect_id as id, " .
				"	name " .
				"from " .
				"	architect " .
				"order by " .
				"	name";
				
		return $sql;
	}
	
	public function getCollection() {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	architect_id as id, " .
				"	name " .
				"from " .
				"	architect " .
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
		
		$id = $database->get('select coalesce(max(architect_id), 0) + 1 from architect');
		
		$sql = "insert into architect (" .
				"	architect_id, " .
				"	name, " .
				"	email, " .
				"	description, " .
				"	phone, " .
				"	cellphone, " .
				"	status, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->name}', " .
				"	'{$data->email}', " .
				"	'{$data->description}', " .
				"	'{$data->phone}', " .
				"	'{$data->cellphone}', " .
				"	{$data->status}, " .
				"	now(), " .
				"	now()" .
				")";
		
		return $database->query($sql) ? (int) $id : 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	architect " .
				"set " .
				"	name = '{$data->name}', " .
				"	email = '{$data->email}', " .
				"	description = '{$data->description}', " .
				"	phone = '{$data->phone}', " .
				"	cellphone = '{$data->cellphone}', " .
				"	status = {$data->status}, " .
				"	date_modified = now() " .
				"where " .
				"	architect_id = {$data->id}";
		
		return $database->query($sql);
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from architect where architect_id = {$id}";
		
		return $database->query($sql);
	}
	
}
?>