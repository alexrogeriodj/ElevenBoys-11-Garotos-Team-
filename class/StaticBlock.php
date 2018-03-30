<?php
final class StaticBlock {

	private $loader;
	
	private $cacheName = 'static-block-';
	
	private $cacheTime = 3600; // segundos
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Cache', 'library');
	}
	
	public function get($name) {
		$database = $this->loader->get('database');
		$cache    = $this->loader->get('cache');
		
		$cacheData = $cache->get($this->cacheName . $name);
		if($cacheData) return $cacheData; 
		
		$sql = "select " .
				"	content as content " .
				"from " .
				"	static_block " .
				"where " .
				"	status = 1 " .
				"	and name = '{$name}'";
		
		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq) > 0) {
			$data = $database->getArray($dbq);
			
			$data['content'] = str_replace('{site_url}', $this->loader->getUrlSite(), $data['content']);
			
			$cache->save($this->cacheName . $name, $data['content'], $this->cacheTime);
			
			return $data['content'];
		}
		
		return '';
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(static_block_id),0) + 1 from static_block');
		
		$sql = "insert into static_block (" .
				"	static_block_id, " .
				"	name, " .
				"	title, " .
				"	content, " .
				"	status, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->name}', " .
				"	'{$data->title}', " .
				"	'{$data->content}', " .
				"	{$data->status}, " .
				"	now(), " .
				"	now() " .
				")";
		
		return $database->query($sql) ? (int) $id : 0;
	}
	
	public function save($data) {
		$database = $this->loader->get('database');
		$cache    = $this->loader->get('cache');
		
		// Recupera o nome para limpar o cache (para casos em que o nome é alterado)
		$name = $database->get("select name from static_block where static_block_id = {$data->id}");
		
		$sql = "update " .
				"	static_block " .
				"set " .
				"	name = '{$data->name}', " .
				"	title = '{$data->title}', " .
				"	content = '{$data->content}', " .
				"	status = {$data->status}, " .
				"	date_modified = now() " .
				"where " .
				"	static_block_id = {$data->id}";
		
		if($database->query($sql)) {
			// Limpa o cache
			$cache->delete($this->cacheName . $name);
			
			return (int) $data->id;
		}
		
		return 0;
	}
	
}
?>