<?php
final class Banner {
	
	const BANNER_TYPE_SIDEBAR = 1;
	const BANNER_TYPE_PRODUCT = 2;
	
	const BANNER_FORMAT_IMAGE = 1;
	const BANNER_FORMAT_HTML = 2;
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Image', 'library');
	}
	
	public function render($data, $wrapClass='') {
		if(!$data) return '';
		
		$html = '';
		
		if($data['format_id'] == self::BANNER_FORMAT_IMAGE) {
			$html .= $this->loader->get('image')->getImage($data['image_id'], $data['image_name']);
			if(!empty($data['link'])) {
				$html = "<a href=\"banner-click.php?id={$data['id']}\" title=\"{$data['link']}\" " . ($data['new_tab'] == 'S' ? ' target="_blank"' : '') . ">{$html}</a>";
			}
		}
		
		else if($data['format_id'] == self::BANNER_FORMAT_HTML)
			$html .= $data['content'];
			
		echo empty($html) ? '' : "<div class=\"banner-content {$wrapClass}\">{$html}</div>";
	}
	
	public function getByType($type, $limit=1) {
		$database = $this->loader->get('database');
		
		$sql = "select 
					a.banner_id as id,
					a.banner_format_id as format_id, 
					a.banner_type_id as type_id, 
					a.link as link, 
					a.new_tab as new_tab, 
					a.content as content, 
					b.image_id as image_id,
					b.name as image_name 
				from 
					banner a 
					left join image b on a.image_id = b.image_id
				where 
					a.status = 1 
					and a.banner_type_id = {$type}
				order by 
					a.date_added desc";
					
		$data = array();
		
		$dbq = $database->query($sql, array(0, $limit));
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
	
		return $data;
	}
	
	public function getFormatCollection($returnSql=false) {
		
		$sql = "select " .
				"	banner_format_id as id, " .
				"	name " .
				"from " .
				"	banner_format " .
				"order by " .
				"	name";
				
		if($returnSql) return $sql;
		
		$database = $this->loader->get('database');
		
		$data = array();
		
		$dbq = $database->query($sql);
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
	
		return $data;
	}
	
	public function getTypeCollection($returnSql=false) {
		
		$sql = "select " .
				"	banner_type_id as id, " .
				"	name " .
				"from " .
				"	banner_type " .
				"order by " .
				"	banner_type_id";
				
		if($returnSql) return $sql;
		
		$database = $this->loader->get('database');
		
		$data = array();
		
		$dbq = $database->query($sql);
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
	
		return $data;
	}
	
	public function getType($id) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	banner_type_id as id, " .
				"	name, " .
				"	width, " .
				"	height " .
				"from " .
				"	banner_type " .
				"where " .
				"	banner_type_id = {$id}";
				
		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq)) 
			return $database->getArray($dbq);
	
		return null;
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(banner_id), 0) + 1 from banner');
		
		$sql = "insert into banner (" .
				"	banner_id, " .
				"	banner_format_id, " .
				"	banner_type_id, " .
				"	title, " .
				"	link, " .
				"	new_tab, " .
				"	content, " .
				"	width, " .
				"	height, " .
				"	status, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	{$data->formatId}, " .
				"	{$data->typeId}, " .
				"	'{$data->title}', " .
				"	'{$data->link}', " .
				"	'{$data->newTab}', " .
				"	'{$data->content}', " .
				"	{$data->width}, " .
				"	{$data->height}, " .
				"	{$data->status}, " .
				"	now(), " .
				"	now()" .
				")";
		
		return $database->query($sql) ? (int) $id : 0;		
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	banner " .
				"set " .
				"	banner_format_id = {$data->formatId}, " .
				"	banner_type_id = {$data->typeId}, " .
				"	title = '{$data->title}', " .
				"	link = '{$data->link}', " .
				"	new_tab = '{$data->newTab}', " .
				"	content = '{$data->content}', " .
				"	width = {$data->width}, " .
				"	height = {$data->height}, " .
				"	status = {$data->status}, " .
				"	date_modified = now() " .
				"where " .
				"	banner_id = {$data->id}";
		
		return $database->query($sql);
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$imgid = $database->get("select image_id from banner where banner_id = {$id}");
			
		$sql = "delete from banner where banner_id = {$id}";
		
		if($database->query($sql)) {
			$this->loader->getObject('Image', 'library')->removeImage($imgid);
			return true;				
		}	
		
		return false;
	}
	
}
?>