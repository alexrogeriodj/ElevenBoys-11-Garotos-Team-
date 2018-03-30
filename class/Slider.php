<?php
final class Slider {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function getData($identifier) {
		$database = $this->loader->get('database');
		$image    = $this->loader->getObject('Image', 'library');
		
		$data = array();
		
		$sql = "select 
					a.slider_id as id, 
					a.identifier, 
					a.name,
					b.slider_item_id as item_id,
					b.title,
					b.link,
					c.image_id,
					c.name as image_name
				from 
					slider a
					inner join slider_item b on a.slider_id = b.slider_id
					inner join image c on b.image_id = c.image_id
				where 
					a.status = 1
					and b.status = 1
					and a.identifier = '{$identifier}'";
		
		$dbq = $database->query($sql);
		
		while($row = $database->getArray($dbq)) {
			$data['id'] 		= $row['id'];
			$data['identifier'] = $row['identifier'];
			$data['name'] 		= $row['name'];
			
			$data['items'][$row['item_id']]['title'] 		= $row['title'];
			$data['items'][$row['item_id']]['link'] 		= $row['link'];
			$data['items'][$row['item_id']]['image'] 		= $image->getImage($row['image_id'], $row['image_name']);
			$data['items'][$row['item_id']]['image_url'] 	= $image->getImage($row['image_id'], $row['image_name'], 'NOTAG');
		}
		
		$data['count'] = count($data['items']);
		
		return $data;
	}
	
	public function get($id) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	slider_id as id, " .
				"	identifier, " .
				"	name,  " .
				"	status, " .
				"	date_added " .
				"from " .
				"	slider a " .
				"where " .
				"	slider_id = {$id}";
		
		$dbq = $database->query($sql, array(0, 1));
		
		if($database->getNumRows($dbq) > 0)
			return $database->getArray($dbq);
		
		return null;
	}
	
	public function getItem($id) {
		$database = $this->loader->get('database');
	
		$sql = "select " .
				"	a.slider_item_id as id, " .
				"	a.slider_id, " .
				"	a.title, " .
				"	a.link, " .
				"	a.text, " .
				"	a.image_id, " .
				"	a.sort_order,  " .
				"	a.status,  " .
				"	a.date_added, " .
				"	b.name " .
				"from " .
				"	slider_item a " .
				"	inner join slider b on a.slider_id = b.slider_id " .
				"where " .
				"	slider_item_id = {$id}";
	
		$dbq = $database->query($sql, array(0, 1));
	
		if($database->getNumRows($dbq) > 0)
			return $database->getArray($dbq);
	
		return null;
	}
	
	public function getCollection($data=array()) {
		$database = $this->loader->get('database');
		
		$result = array(
			'items' => null,
			'count' => 0
		);
		
		$and = '';
		
		$sql = "select " .
				"	slider_id as id, " . 
				"	identifier, " .
				"	name,  " .
				"	status, " .
				"	date_added " . 
				"from " .
				"	slider a " . 
				"where " . 
				"	1=1 {$and} " .
				"order by " .
				"	name";
		
		$start = empty($data['start']) ? 0 : (int) $data['start'];
		$limit = empty($data['limit']) ? 13 : (int) $data['limit'];
		
		if($start > 0) $start = ($start - 1) * $limit;
		
		$dbq = $database->query($sql, array($start, $limit));
		
		if($database->getNumRows($dbq) > 0) {
			
			$count = $database->get("select count(slider_id) from slider a where 1=1 {$and}");
				
			$result['count'] = $count;
		
			while($row = $database->getArray($dbq)) {
				$result['items'][$row['id']] = $row;
			}
		
		}
		
		return $result;
	}
	
	public function getItemCollection($data=array()) {
		$database = $this->loader->get('database');
	
		$result = array(
			'items' => null,
			'count' => 0
		);
	
		$and = '';
	
		$sql = "select " .
				"	a.slider_item_id as id, " .
				"	a.slider_id, " .
				"	a.title,  " .
				"	a.link,  " .
				"	a.sort_order,  " .
				"	a.status, " .
				"	a.date_added " .
				"from " .
				"	slider_item a " .
				"where " .
				"	1=1 {$and} " .
				"order by " .
				"	sort_order";
	
		$start = empty($data['start']) ? 0 : (int) $data['start'];
		$limit = empty($data['limit']) ? 13 : (int) $data['limit'];

		if($start > 0) $start = ($start - 1) * $limit;

		$dbq = $database->query($sql, array($start, $limit));

		if($database->getNumRows($dbq) > 0) {
				
			$count = $database->get("select count(slider_item_id) from slider_item a where 1=1 {$and}");

			$result['count'] = $count;

			while($row = $database->getArray($dbq)) {
				$result['items'][$row['id']] = $row;
			}

		}

		return $result;
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(slider_id), 0) + 1 from slider');
		
		$sql = "insert into slider(" .
				"	slider_id, " .
				"	identifier, " .
				"	name, " .
				"	status, " .
				"	account_id, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->identifier}', " .
				"	'{$data->name}', " .
				"	{$data->status}, " .
				"	{$data->account_id}, " .
				"	now(), " .
				"	null" .
				")";
		
		return $database->query($sql) ? (int) $id : 0;
	}
	
	public function addItem($data) {
		$database = $this->loader->get('database');
	
		$id = $database->get('select coalesce(max(slider_item_id), 0) + 1 from slider_item');
	
		$sql = "insert into slider_item(" .
				"	slider_item_id, " .
				"	slider_id, " .
				"	title, " .
				"	link, " .
				"	text, " .
				"	sort_order, " .
				"	status, " .
				"	account_id, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	{$data->slider_id}, " .
				"	'{$data->title}', " .
				"	'{$data->link}', " .
				"	'{$data->text}', " .
				"	{$data->sort_order}, " .
				"	{$data->status}, " .
				"	{$data->account_id}, " .
				"	now(), " .
				"	null" .
				")";
	
		return $database->query($sql) ? (int) $id : 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	slider " .
				"set " .
				"	identifier = '{$data->identifier}', " .
				"	name = '{$data->name}', " .
				"	status = {$data->status}, " .  
				"	account_id = {$data->account_id}, " .
				"	date_modified = now() " .
				"where " .
				"	slider_id = {$data->id}";
		
		return $database->query($sql);
	}
	
	public function editItem($data) {
		$database = $this->loader->get('database');
	
		$sql = "update " .
				"	slider_item " .
				"set " .
				"	title = '{$data->title}', " .
				"	link = '{$data->link}', " .
				"	text = '{$data->text}', " .
				"	sort_order = {$data->sort_order}, " .
				"	status = {$data->status}, " .
				"	account_id = {$data->account_id}, " .
				"	date_modified = now() " .
				"where " .
				"	slider_item_id = {$data->id}";
	
		return $database->query($sql);
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from slider where slider_id = {$id}";
		
		return $database->query($sql);
	}
	
	public function deleteItem($id) {
		$database = $this->loader->get('database');
	
		$sql = "select image_id from slider_item where slider_item_id = {$id}";
		$imageId = $database->get($sql);
		
		$sql = "delete from slider_item where slider_item_id = {$id}";
		$database->query($sql);
		
		return $image->removeImage($imageId);
	}
	
}
?>