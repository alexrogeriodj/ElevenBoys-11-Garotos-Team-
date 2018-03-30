<?php
final class Seo {
	
	private $loader;
	
	private $cacheEnabled = true;
	
	private $cacheTime = 3600;
	
	private $cacheName = 'seo-';
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('Cache', 'library');
	}
	
	public function setCacheEnabled($cacheEnabled) {
		$this->cacheEnabled = $cacheEnabled;
	}

	public function get($name, $query=null) {
		$database = $this->loader->getObject('DataBase', 'database');
		$cache    = $this->loader->get('cache');
		
		$query = "{$name}.php" . ($query ? "?{$query}" : '');
		
		if($this->cacheEnabled) {
			$cacheData = $cache->get($this->cacheName . md5($query));
			if($cacheData) return $cacheData;
		} 
		
		$data = array(
			'title' 		=> '', 
			'description' 	=> '',
			'keywords' 		=> '',
			'robots' 		=> ''
		);
		
		$sql = "select " .
				"	title, " .
				"	description, " .
				"	keywords, " .
				"	robots " .
				"from " .
				"	 seo " .
				"where " .
				"	query = '{$query}'";
				
		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq) > 0) {
			$data = $database->getArray($dbq);
			
			if($this->cacheEnabled)
				$cache->save($this->cacheName . md5($query), $data, $this->cacheTime);
		}
			
		return $data;
	}
	
	public function field($name, $query=null) {
		
		$data = $this->get($name, $query);
		
		$html = '<div class="widget">' .
					'<div class="whead">' .
						'<h5>SEO - Search Engine Optimization</h5>' .
					'</div>' .
					'<div class="wbody">' .
						'<div class="field colfull" id="in-seo_title">' .
							'<label for="fd-seo_title">Título</label>' .
							'<input type="text" name="seo_title" id="fd-seo_title" value="' . $data['title'] . '" size="70" maxlength="70">' .
						'</div>' .
						'<div class="field colfull" id="in-seo_description">' .
							'<label for="fd-seo_description">Description</label>' .
							'<textarea name="seo_description" id="fd-seo_description" rows="3">' . $data['description'] . '</textarea>' .
						'</div>' .
						'<div class="field colfull" id="in-seo_keywords">' .
							'<label for="fd-seo_keywords">Keywords</label>' .
							'<textarea name="seo_keywords" id="fd-seo_keywords" rows="3">' . $data['keywords'] . '</textarea>' .
						'</div>' .
					'</div>' .
				'</div>';
				
		echo $html;
	}
	
	public function save($name, $query, $data) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$query = "{$name}.php" . ($query ? "?{$query}" : '');
		
		$sql = "select 1 from seo where query = '{$query}'";
		if($database->get($sql) == '1') {
			$sql = "update " .
					"	seo " .
					"set " .
					"	title = '{$data->title}', " .
					"	description = '{$data->description}', " .
					"	keywords = '{$data->keywords}', " .
					"	robots = '{$data->robots}' " .
					"where " .
					"	query = '{$query}'";
					
			if($database->query($sql)) {
				$this->loader->get('cache')->delete($this->cacheName . md5($query));
				return true;
			}
		}
		else {
			$id = $database->get('select coalesce(max(seo_id), 0) + 1 from seo');
			
			$sql = "insert into seo (" .
					"	seo_id, " .
					"	query, " .
					"	title, " .
					"	description, " .
					"	keywords, " .
					"	robots " .
					") " .
					"values (" .
					"	{$id}, " .
					"	'{$query}', " .
					"	'{$data->title}', " .
					"	'{$data->description}', " .
					"	'{$data->keywords}', " .
					"	'{$data->robots}' " .
					")";
					
			if($database->query($sql)) return true;
		}
		
		return false;		
	}
	
	public function remove($query) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "delete from seo where query = '{$query}'";
		return $database->query($sql);
	}
	
	public function has($query) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "select 1 from seo where lower(query) = lower('{$query}')";
		return $database->get($sql) == '1'; 
	}

}
?>