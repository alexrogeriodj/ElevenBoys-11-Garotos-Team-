<?php
final class Post {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Cache', 'library');
		$this->loader->requireObject('Functions', 'library');
	}
	
	public function formatDate($date, $format='d \d\e F \d\e Y') {
		if(empty($date)) return '';
		
		$functions = $this->loader->get('functions');
		
		$dateTs = strtotime($date);
		
		$format = str_replace('F', '$1', $format);
		$format = str_replace('M', '$2', $format);
		
		$format = date($format, $dateTs);
		
		$format = str_replace('$1', $functions->getNameMonth(date('n', $dateTs)), $format);
		$format = str_replace('$2', $functions->getNameMonth(date('n', $dateTs), true), $format);
		
		return $format;
	}
	
	public function getArchiveSummary() {
		$database = $this->loader->get('database');
		$cache    = $this->loader->get('cache');
		
		$data = $cache->get('archive-summary');
		if(!$data)  { 
		
			$sql = "select " .
					"	count(*) as count, " .
					"	date_format(date_added, '%Y-%m') as date " .
					"from " .
					"	post " .
					"group by " .
					"	date_format(date_added, '%Y-%m')";
					
			$dbq = $database->query($sql);
					
			$dbq = $database->query($sql);
			while($row = $database->getArray($dbq)) {
				$date = explode('-', $row['date']);
			
				$data[$date[0]][$date[1]] = $row['count'];
			}
			
			$cache->save('archive-summary', $data, 3600);
			
		}
		
		return $data;
	}
	
	public function getArchive($data=array()) {
		$database = $this->loader->get('database');
		
		$result = array(
			'itens' => null, 
			'count' => 0
		);
		
		$and = " and status = 1";
		
		$sql = "select " .
				"	a.post_id as id, " .
				"	a.title as title, " .
				"	a.date_added as date " .
				"from " .
				"	post a " .
				"where 1=1 " .
				"	{$and} " .
				"order by " .
				"	a.date_added desc";
				
		$start = empty($data['start']) ? 0 : (int) $data['start'];
		$limit = empty($data['limit']) ? 20 : (int) $data['limit'];
		
		if($start > 0) $start = ($start - 1) * $limit;
		
		$dbq = $database->query($sql, array($start, $limit));
		
		if($database->getNumRows($dbq) > 0) {
			
			$count = $database->get("select count(post_id) from post a where 1=1 {$and}");
				
			$result['count'] = $count;
			
			while($row = $database->getArray($dbq)) {
				$dateTs = strtotime($row['date']);
			
				$result['itens'][date('Y', $dateTs)][date('n', $dateTs)][] = $row;
			}
	
		}
		
		return $result;
	}
	
	public function getMostViewed($limit=10) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	post_id as id, " .
				"	title, " .
				"	date_added as date " .
				"from " .
				"	post " .
				"where " .
				"	status = 1 " .
				"order by " .
				"	views desc, " .
				"	date_added desc";
				
		$dbq = $database->query($sql, array(0, $limit));
		
		$data = array();
		
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
		
		return $data;
	}
	
	public function getRelated($data, $limit=5) {
		$database = $this->loader->get('database');
		
		$inCats = '';
		if(count($data['categories'])) {
			
			$cats = array();
			foreach($data['categories'] as $cat) {
				$cats[] = $cat['id'];
			}
			
			$inCats = 'and d.category_id in (' . join(',', $cats) . ')';
		}
		
		$sql = "select 
					a.post_id as id,
					a.title as title,
					c.image_id as image_id,
					c.name as image_name,
					case when max(d.category_id) is null then 0 else 1 end as sort_order
				from 
					post a 
					left join (
						post_image b inner join image c on b.image_id = c.image_id
					) on a.post_id = b.post_id
					left join post_category d on a.post_id = d.post_id 
				where
					a.status = 1 
					{$inCats}
					and a.post_id != {$data['id']}
				group by 
					a.post_id,
					a.title, 
					c.image_id, 
					c.name
				order by
					5 desc, 
					a.date_added desc";
		
		$dbq = $database->query($sql, array(0, $limit));
		
		$data = array();
		
		while($row = $database->getArray($dbq)) {
			$data[$row['id']] = $row;
		}
		
		return $data;
	}
	
	public function get($id, $params=array()) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	a.post_id as id, " .
				"	a.title as title, " .
				"	a.content as content, " .
				"	a.summary as summary, " .
				"	a.tags as tags, " .
				"	a.architect_id as architect_id, " .
				"	a.date_added as date, " .
				"	l.name as author, " .
				"	c.image_id as image_id, " .
				"	c.name as image_name, " .
				"	c.legend as image_legend " .
				"from " .
				"	post a " .
				"	" .
				"	inner join account l on a.account_id = l.account_id " .
				"	" .
				"	left join (" .
				"		post_image b inner join image c on b.image_id = c.image_id and b.featured = 1 " .
				"	) on a.post_id = b.post_id " .
				"where 1=1 " .
				"	and a.post_id = " . (int) $id;
		
		if(!isset($params['ignoreStatus'])) $params['ignoreStatus'] = false;
		
		if(!$params['ignoreStatus']) 
			$sql .= " and a.status = 1";
				
		$dbq = $database->query($sql, array(0, 1));
		
		if($database->getNumRows($dbq) > 0) {
			$data = $database->getArray($dbq);
			
			if(isset($params['loadCats']) && $params['loadCats'])
				$data['categories'] = $this->getCategories($data['id']);
				
			if(isset($params['loadGallery']) && $params['loadGallery'])
				$data['gallery'] = $this->getGalleryImages($data['id']);
				
			if(isset($params['loadSignature']) && $params['loadSignature'] && !empty($data['architect_id']))
				$data['signature'] = $this->loader->getObject('Architect', 'class', false)->get($data['architect_id']);
			
			return $data;
		}
		
		return null;
	}
	
	public function getAllImages($postId) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	b.image_id as id, " .
				"	b.name, " .
				"	b.legend, " .
				"	a.featured " .
				"from " .
				"	post_image a " .
				"	join image b on a.image_id = b.image_id " .
				"where " .
				"	a.post_id = {$postId} " .
				"order by " .
				"	a.featured desc, " .
				"	a.image_id ";
		
		$data = array();
		
		$dbq = $database->query($sql);
		while($row = $database->getArray($dbq)) {
			$data[] = $row;
		}
		
		return $data;
	}
	
	public function getGalleryImages($postId) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	b.image_id as id, " .
				"	b.name, " .
				"	b.legend " .
				"from " .
				"	post_image a " .
				"	join image b on a.image_id = b.image_id " .
				"where " .
				"	a.featured = 0 " .
				"	and a.post_id = {$postId}";
								
		$data = array();
		
		$dbq = $database->query($sql);
		while($row = $database->getArray($dbq)) {
			$data[] = $row;
		}
		
		return $data;
	}
	
	public function getCategories($postId) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	a.category_id as id, " .
				"	b.name " .
				"from " .
				"	post_category a " .
				"	inner join category b on a.category_id = b.category_id " .
				"where " .
				"	a.post_id = {$postId}";
				
		$data = array();
		
		$dbq = $database->query($sql);
		while($row = $database->getArray($dbq)) {
			$data[] = $row;
		}
		
		return $data;
	}
	
	public function getList($data) {
		$database  = $this->loader->get('database');
		$functions = $this->loader->get('functions');
		
		$result = array(
			'itens' => null, 
			'count' => 0
		);
		
		$and = " and a.status = 1";
		
		if(isset($data['filter'])) {
			
			if(isset($data['filter']['cat']) && $data['filter']['cat'] > 0)
				$and .= " and a.post_id in (select post_id from post_category where category_id = {$data['filter']['cat']})";
			
			if(isset($data['filter']['ignore']))
				$and .= ' and a.post_id not in (' . $data['filter']['ignore'] . ')';
				
			if(isset($data['filter']['catIgnore']))
				$and .= " and a.post_id not in (select post_id from post_category where category_id = {$data['filter']['catIgnore']})";
				
			if(isset($data['filter']['search'])) {
				$and .= " and (" .
						"	upper(a.title) like upper('%{$data['filter']['search']}%')" .
						"	or upper(a.content) like upper('%{$data['filter']['search']}%')" .
						")";
			}
				
			$dateIni = isset($data['filter']['dateIni']) ? $functions->formatDate($data['filter']['dateIni'], 'SQL') . ' 00:00:00' : null;
			$dateEnd = isset($data['filter']['dateEnd']) ? $functions->formatDate($data['filter']['dateEnd'], 'SQL') . ' 23:59:59' : null;
				
			if($dateIni && $dateEnd) 
				$and .= " and a.date_added between '{$dateIni}' and '{$dateEnd}'";
			
			else if($dateIni)
				$and .= " and a.date_added >= '{$dateIni}'";
				
			else if($dateEnd)
				$and .= " and a.date_added <= '{$dateEnd}'";
			
		}
		
		$sql = "select " .
				"	a.post_id as id, " .
				"	a.title, " .
				"	a.content, " .
				"	a.summary, " .
				"	a.tags, " .
				"	a.date_added as date, " .
				"	c.image_id, " .
				"	c.name as image_name, " .
				"	c.legend as image_legend, " .
				"	d.architect_id as architect_id, " .
				"	d.name as architect_name, " .
				"	d.email as architect_email, " .
				"	d.description as architect_description, " .
				"	d.phone as architect_phone, " .
				"	d.cellphone as architect_cellphone, " .
				"	e.image_id as architect_image_id, " .
				"	e.name as architect_image_name " .
				"from " .
				"	post a " .
				"	" .
				"	left join (" .
				"		post_image b inner join image c on b.image_id = c.image_id and b.featured = 1 " .
				"	) on a.post_id = b.post_id " .
				"	" .
				"	left join architect d on a.architect_id = d.architect_id " .
				"	" .
				"	left join image e on d.image_id = e.image_id " .
				"where 1=1 " .
				"	$and";
				
		$sql .= " order by " . (isset($data['sort']) ? $data['sort'] : 'a.date_added');
		$sql .= isset($data['order']) && ($data['order'] == 'DESC') ? ' desc' : '';
		
		$start = empty($data['start']) ? 0 : (int) $data['start'];
		$limit = empty($data['limit']) ? 6 : (int) $data['limit'];
		
		if($start > 0) $start = ($start - 1) * $limit;
		
		$dbq = $database->query($sql, array($start, $limit));
		
		if($database->getNumRows($dbq) > 0) {
			
			$count = $database->get("select count(post_id) from post a where 1=1 $and");
				
			$result['count'] = $count;
			
			$convert = array(
				'architect_id'			=> 'id', 
				'architect_name'		=> 'name', 
				'architect_email'		=> 'email', 
				'architect_description'	=> 'description', 
				'architect_phone'		=> 'phone', 
				'architect_cellphone'	=> 'cellphone', 
				'architect_image_id'	=> 'image_id', 
				'architect_image_name'	=> 'image_name'
			);
			
			while($row = $database->getArray($dbq)) {
				
				$newName = 'signature';
				foreach($convert as $fieldKey => $fieldName) {
					$row[$newName][$fieldName] = $row[$fieldKey];
					unset($row[$fieldKey]);
				}
				if(empty($row[$newName]['id'])) unset($row[$newName]);
				
				$result['itens'][] = $row;
			}
	
		}
		
		return $result;
	}
	
	public function getFeaturedImage($postId) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	b.image_id as id, " .
				"	b.name as name, " .
				"	b.legend as legend " .
				"from " .
				"	post_image a " .
				"	inner join image b on a.image_id = b.image_id " .
				"where " .
				"	a.post_id = {$postId} " .
				"	and a.featured = 1";
				
		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq) > 0) {
			return $database->getArray($dbq);
		}
		
		return null;
	}
	
	public function getCategoryIds($postId) {
		$database = $this->loader->get('database');
		
		$sql = "select category_id from post_category where post_id = {$postId}";
		
		$dbq = $database->query($sql);
		
		$data = array();
		
		while($row = $database->getArray($dbq)) {
			$data[] = $row['category_id'];
		}
		
		return $data;
	}
	
	public function updateViews($postId) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	post " .
				"set " .
				"	views = coalesce(views, 0) + 1 " .
				"where " .
				"	post_id = {$postId}";
		
		return $database->query($sql);
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(post_id), 0) + 1 from post');
		
		$sql = "insert into post (" .
				"	post_id, " .
				"	title, " .
				"	content, " .
				"	summary, " .
				"	tags, " .
				"	status, " .
				"	architect_id, " .
				"	account_id, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->title}', " .
				"	'{$data->content}', " .
				"	'{$data->summary}', " .
				"	'{$data->tags}', " .
				"	{$data->status}, " .
				"	" . $data->getIfEmpty('architectId', 'null') . ", " .
				"	{$data->userId}, " .
				"	now(), " .
				"	now()" .
				")";
		
		if($database->query($sql)) {
			$this->addCategories($id, $data->categories);
			
			return (int) $id;
		}
		
		return 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "update " .
				"	post " .
				"set " .
				"	title = '{$data->title}', " .
				"	content = '{$data->content}', " .
				"	summary = '{$data->summary}', " .
				"	tags = '{$data->tags}', " .
				"	status = {$data->status}, " .
				"	architect_id = " . $data->getIfEmpty('architectId', 'null') . ", " .
				"	date_modified = now() " .
				"where " .
				"	post_id = {$data->id}";
		
		if($database->query($sql)) {
		
			if($this->deleteCategories($data->id)) 
				$this->addCategories($data->id, $data->categories);
				
			return true;
		}
		
		return false;
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$error = false;
		
		// Image
		if(!$error) {
			$image = $this->loader->getObject('Image', 'library');
			
			$sql = "select image_id from post_image where post_id = {$id}";
			$dbq = $database->query($sql);
			
			$sql = "delete from post_image where post_id = {$id}";
			$error = $database->query($sql) ? false : true;
			
			while($row = $database->getArray($dbq)) {
				$image->removeImage($row['image_id']);
			}
		}
		
		// Category
		if(!$error) {
			$sql = "delete from post_category where post_id = {$id}";
			$error = $database->query($sql) ? false : true;
		}
		
		// Post
		if(!$error) {
			$sql = "delete from post where post_id = {$id}";
			$error = $database->query($sql) ? false : true;
		}
		
		return !$error;
	}
	
	public function addCategories($postId, $cats) {
		$database = $this->loader->get('database');
		
		$sql = "insert into post_category (" .
				"	post_id, " .
				"	category_id" .
				") " .
				"values (" .
				"	{$postId}, " .
				"	%s" .
				")";
			
		foreach($cats as $catId) {
			$database->query(sprintf($sql, $catId));
		}
		
		return true;
	}
	
	public function deleteCategories($postId) {
		$database = $this->loader->get('database');
		
		$sql = "delete from post_category where post_id = {$postId}";
		
		return $database->query($sql);
	}
	
}
?>