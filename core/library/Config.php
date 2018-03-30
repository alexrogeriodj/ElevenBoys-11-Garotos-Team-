<?php
final class Config {
	
	private $data = array();
	private $loader;
	private $error;

	function __construct($loader, $filename='common', $dir=null) {
		$this->loader = $loader;
		if($filename) $this->load($filename, $dir);
	}  

  	public function get($key) {
    	return (isset($this->data[$key]) ? $this->data[$key] : null);
  	}	
	
	public function set($key, $value) {
    	$this->data[$key] = $value;
  	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}

	public function databaseLoad($configs=null) {
		$database = $this->loader->getObject('DataBase', 'database');
		$cache    = $this->loader->getObject('Cache', 'library');
		
		$cacheName = 'config.' . (is_null($configs) ? 'all' : md5(join('', $configs)));
		
		$data = $cache->get($cacheName);
		if(!$data) {
		
			$sql = "select " .
					"	name, " .
					"	content " .
					"from " .
					"	config ";
			
			if(!is_null($configs)) {
				$sql .= " where name in ('" . join("','", $configs) . "')";
			}
			
			$dbq = $database->query($sql);
			while($row = $database->getArray($dbq)) {
				$data[$row['name']] = $row['content'];
			}
			
			$cache->save($cacheName, $data);
			
		}
		
		$this->data = array_merge($this->data, $data);	
	}
	
	public function databaseSave($name, $content) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "update " .
				"	config " .
				"set " .
				"	content = '{$content}', " .
				"	date_modified = now() " .
				"where " .
				"	name = '{$name}'";
		
		if($database->query($sql)) {
			$this->loader->getObject('Cache', 'library')->delete('config.*', true);
			return true;
		}
		
		return false;
	}
	
	public function databaseEdit($data) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "update " .
				"	config " .
				"set " .
				"	name = '$data->name', " .
				"	description = '$data->description', " .
				"	help_info = '$data->helpInfo', " .
				"	config_type_id = $data->typeId, " .
				"	content = '$data->content', " .
				"	sort_order = $data->sortOrder, " .
				"	account_id = $data->userId, " .
				"	date_modified = now() " .
				"where " .
				"	config_id = {$data->id}";
				
		if($database->query($sql)) {
			$this->loader->getObject('Cache', 'library')->delete('config.*', true);
			return true;
		}
		
		return false;
	}
	
	public function databaseAdd($data) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$id = $database->get('select coalesce(max(config_id), 0) + 1 from config');
		
		$sql = "insert into config (" .
				"	config_id, " .
				"	name, " .
				"	description, " .
				"	help_info, " .
				"	config_type_id, " .
				"	content, " .
				"	sort_order, " .
				"	account_id, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'$data->name', " .
				"	'$data->description', " .
				"	'$data->helpInfo', " .
				"	$data->typeId, " .
				"	'$data->content', " .
				"	$data->sortOrder, " .
				"	$data->userId, " .
				"	now(), " .
				"	now()" .
				")";
				
		return $database->query($sql) ? (int) $id : 0;
	}
	
  	public function load($filename, $dir=null) {
  		
  		$dirConfig = $dir ? $dir : 'config';
  		$file = $this->loader->getRootPath() . "/$dirConfig/$filename.php";
		
    	if (file_exists($file)) { 
	  		$cfg = array();
	  
	  		require($file);
	  
	  		$this->data = array_merge($this->data, $cfg);
		} 
		else $error = 'Não pode carregar a configuração ' . $filename . '!';
		
		return !$this->error;
		
	}
	
	public function getError() {
		return $this->error;
	}
  	
}
?>