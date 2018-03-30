<?php
ini_set('memory_limit', '64M');
final class Image {
	
	private $loader;
	private $rootPath;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->rootPath = $this->loader->getRootPath();
	}
	
	function getDynamicImage($match) {
		$ID=$NAME=$PARAM=null;
		
		$prms = explode(' ', $match[1]);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		return $this->getImage($ID, $NAME, $PARAM);
	}
	
	function getDynamicImages($content) {
		return preg_replace_callback('|\[IMG(.*?)\]|', array(&$this, 'getDynamicImage'), $content);
	}
	
	function getImagePath($id, $name) {
		if(empty($id) || empty($name)) return '';
		$config = $this->loader->get('config');
		$config->load('upload');
		return $this->rootPath . '/' . $config->get('UP_DIR_IMG') . '/' . $id . '/' . $name;
	}
	
	function getImage($id, $name, $prm='') {
		
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Functions', 'library');
		
		//$urlsite = $this->loader->get('functions')->getParam('URLSIT');
		$urlsite = $this->loader->getUrlSite();
		
		$config = $this->loader->get('config');
		$config->load('upload');
			
		$imgdir = $config->get('UP_DIR_IMG');
		
		if(!empty($id)) {
			
			if(empty($name)) 
				$name = $this->loader->get('database')->get("select name from image where image_id = {$id}");
			
			$src = "$urlsite/$imgdir/$id/$name";
			$img = "$this->rootPath/$imgdir/$id/$name";
			
		}
		else if(isset($SRC)) {
			$src = $SRC;
			$img = str_replace($urlsite, $this->rootPath, $src);
		}
		else if(isset($NOIMAGE)) {
			$src = $NOIMAGE;
			$img = str_replace($urlsite, $this->rootPath, $src);
		}
		else return '';
		
		$id = isset($ID) ? $ID : $id;
		$alt = isset($ALT) ? $ALT : $name;
		$class = isset($CLASS) ? " class=\"$CLASS\"" : '';
		
		$width = isset($W) ? $W : null;
		$height = isset($H) ? $H : null;
		
		if(isset($CREATE)) $CACHE = true;
		
		if(!empty($id) and isset($CACHE) and ($width or $height)) {
			
			if(!$width) 
				list($width, $height) = $this->getImageSize($img, null, $height);
			else if(!$height) 
				list($width, $height) = $this->getImageSize($img, $width, null);
					
			$ext = strrchr($name, ".");
			$name = str_replace($ext, '', $name) . '-' . $width . 'x' . $height;
			
			$cacheDir = $config->get('CACHE_DIR_IMG');
			$newimg = "{$this->rootPath}/{$cacheDir}/{$id}-{$name}{$ext}";
			
			if(!file_exists($newimg)) {
				
				$this->loader->requireObject('Canvas', 'library', true, false);
				
				$canvas = $this->loader->get('canvas');
				$canvas->set_quality(isset($QUALITY) ? $QUALITY : 90);
				$canvas->load($img);
				$canvas->resize($width, $height, isset($METHOD) ? $METHOD : null);
				$canvas->save($newimg);
				
			}
			
			$src = "{$urlsite}/{$cacheDir}/{$id}-{$name}{$ext}";
			
		}
		else list($width, $height) = $this->getImageSize($img, $width, $height);
		
		if(isset($NOTAG)) return $src;
		else return "<img src=\"$src\" id=\"img-$id\" width=\"$width\" height=\"$height\" alt=\"$alt\"$class />";
		
	}
	
	function getImageSize($src, $width=null, $height=null) {
		
		list($w, $h) = getimagesize($src);
		$newWidth = array($w, $h);
		
		if($height and $h > $height) 
			$newWidth = array(round($w/($h/$height)), $height);
		
		if($width and $w > $width) 
			$newWidth = array($width, round($h/($w/$width)));
		
		return $newWidth;
	}
	
	function setCaption($id, $caption) {
		$database = $this->loader->getObject('DataBase', 'database');
		
		$sql = "update " .
				"	image " .
				"set " .
				"	legend = '{$caption}' " .
				"where " .
				"	image_id = {$id}";
		
		return $database->query($sql);
	}
	
	function removeImage($id) {
		$erro = false;
		
		if(!empty($id)) {
			
			$database = $this->loader->getObject('DataBase', 'database');
			
			$sql = "delete from image where image_id = {$id}";
			if($database->query($sql)) {
				
				$config = $this->loader->get('config');
				$config->load('upload');
			
				$updir = $config->get('UP_DIR_IMG');
				$dir = "{$this->rootPath}/{$updir}/{$id}";
				
				if(is_dir($dir)) {
					
					$read = opendir($dir);
					while($item = readdir($read)) {
						
						if($item == "." or $item == "..") continue;
						if(is_file("{$dir}/{$item}")) if(!unlink("{$dir}/{$item}")) $erro = true;
						
					}
					closedir($read);
					
					if(!$erro) if(!rmdir($dir)) $erro = true;
					
				}

			}
			else $erro = true;
	
		}
		
		return !$erro;
	}	
	
}
?>