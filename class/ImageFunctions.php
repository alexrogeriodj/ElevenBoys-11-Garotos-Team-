<?php
final class ImageFunctions {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function addImageFromUrl($url, $name, $title, $accountId, $maxWidt=900, $quality=90, $qualityResize=100) { 
		
		$result = array();
		
		$imageId = $this->addImage($name, $title, $accountId);
		
		if($imageId) {
			
			$canvas = $this->loader->getObject('Canvas', 'library', true, false);
			$config = $this->loader->get('config');
			
			$config->load('upload');
			
			$rootPath = $this->loader->getRootPath();
			$updir    = $config->get('UP_DIR_IMG');
			
			$dir = "{$rootPath}/{$updir}/{$imageId}";
			mkdir($dir, 0755, true);
			
			$canvas->set_quality($quality);
			
			$canvas->load_url($url);
			
			if($canvas->save("{$dir}/{$name}") !== false) {
				$this->resizeImage("{$dir}/{$name}", $maxWidt, $qualityResize);
				$result['id']   = $imageId;
				$result['dir']  = $dir;
				$result['file'] = "{$dir}/{$name}";
				$result['src']  = $this->loader->getUrlSite() . "/{$updir}/{$imageId}/{$name}";
			}
			else {
				$this->deleteImage($imageId);
				rmdir($dir);
				$result['error'] = 2;
			}
			
		}
		else $result['error'] = 1;
		
		return $result;
	}

	public function deleteImage($imageId) {
		$sql = "delete from " .
				"	image " .
				"where " .
				"	image_id = $imageId";
				
		return $this->loader->get('database')->query($sql);
	}
	
	public function addImage($name, $title, $accountId) {
		$database = $this->loader->get('database');
		
		$id = $database->get('select coalesce(max(image_id), 0) + 1 from image');
		
		$sql = "insert into image (" .
				"	image_id, " .
				"	name, " .
				"	legend, " .
				"	status, " .
				"	account_id, " .
				"	date_added" .
				") " .
				"values (" .
				"	$id, " .
				"	'$name', " .
				"	'$title', " .
				"	1, " .
				"	$accountId, " .
				"	now()" .
				")";
		
		return $database->query($sql) ? (int) $id : null;
	}
	
	public function resizeImage($fileName, $maxWidth=900, $quality=90) {
		$size = getimagesize($fileName);
		
		if($size[0] > $maxWidth) {
		
			$canvas = $this->loader->getObject('Canvas', 'library', true, false);
			
			$canvas->set_quality($quality);
			
			$canvas->load($fileName);
			$canvas->resize($maxWidth, null);
			$canvas->save($fileName);
			
		}
	}

}
?>