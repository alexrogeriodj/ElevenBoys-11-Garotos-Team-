<?php
final class Video {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function get($id) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	video_id as id, " .
				"	title as title, " .
				"	type as type, " .
				"	link as link, " .
				" 	status as status " .
				"from " .
				"	video " .
				"where " .
				"	video_id = {$id}";
		
		$dbq = $database->query($sql);
		
		if($database->getNumRows($dbq) > 0) {
			return $database->getArray($dbq);
		}
		
		return null;
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = (int) $database->get('select coalesce(max(video_id), 0) + 1 from video');
		
		$sql = "insert into video (" .
				"	video_id, " .
				"	title, " .
				"	type, " .
				"	link, " .
				"	status, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->title}', " .
				"	'{$data->type}', " .
				"	'{$data->link}', " .
				"	{$data->status}, " .
				"	now(), " .
				"	now()" .
				")";
		
		if($database->query($sql)) {
			$this->setImage($id, $data->type, $data->link);
			return $id;
		}
		
		return 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		
		$sql = "select " .
				"	type as type, " .
				"	link as link " .
				"from " .
				"	video " .
				"where " .
				"	video_id = {$data->id}";
				
		$oldData = $database->getArray($database->query($sql));
		
		$sql = "update " .
				"	video " .
				"set " .
				"	title = '{$data->title}', " .
				"	type = '{$data->type}', " .
				"	link = '{$data->link}', " .
				"	status = {$data->status}, " .
				"	date_modified = now() " .
				"where " .
				"	video_id = {$data->id}";
		
		if($database->query($sql)) {
			if($data->link != $oldData['link'] || $data->type != $oldData['type'])
				$this->setImage($data->id, $data->type, $data->link, true);
			
			return true;
		}
		
		return false;
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from video where video_id = {$id}";
		
		return $database->query($sql);
	}
	
	public function setImage($videoId, $type, $link, $removeOld=false) {
		if(empty($link)) return false;
		
		$database = $this->loader->get('database');
		$imageFn  = $this->loader->getObject('ImageFunctions', 'class', false);
		$player   = $this->loader->getObject('Player', 'class', false);
			
		if($type == 'Y') {
			$videoCode = $player->getCodeYoutube($link);
			$url = "http://i.ytimg.com/vi/{$videoCode}/maxresdefault.jpg";

			$headers = get_headers($url);
			
			if($headers[0] == 'HTTP/1.0 404 Not Found') 
				$url = "http://i.ytimg.com/vi/{$videoCode}/hqdefault.jpg";
		}
		else {
			$videoCode = $player->getCodeVimeo($link);
			
			$jsonString = file_get_contents("http://vimeo.com/api/v2/video/{$videoCode}.json");
			$json = json_decode($jsonString);
			
			$url = trim($json[0]->thumbnail_large);
		}
		
		$name  = 'video-preview.jpg';
		$title = 'Imagem - vídeo preview';
		
		$result = $imageFn->addImageFromUrl($url, $name, $title, 1, 960);
		
		if(!isset($result['error'])) {
			
			$imgOld = null;
			if($removeOld)
				$imgOld = $database->get("select image_id from video where video_id = {$videoId}");
			
			$sql = "update " .
					"	video " .
					"set " .
					"	image_id = {$result['id']} " .
					"where " .
					"	video_id = {$videoId}";
					
			if($database->query($sql)) {
				if($removeOld) 
					$this->loader->getObject('Image', 'library')->removeImage($imgOld);
				
				return true;
			}
			
		}
		
		return false;
	}
	
}
?>