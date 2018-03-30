<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

if(isset($id)) {

	$database  = $loader->getObject('DataBase', 'database');
	$functions = $loader->getObject('Functions', 'library');
	
	$config = $loader->get('config');
	$config->load('banner');
	
	$types = $config->get('BANNER_TYPE');
	$dir   = $config->get('UP_DIR_BANNER');
	$url   = $loader->getUrlSite();
	
	$sql = "select " .
			"	a.banner_id, " .
			"	a.banner_type_id as type_id, " .
			"	a.title, " .
			"	a.link, " .
			"	a.content, " .
			"	a.width, " .
			"	a.height " .
			"from " .
			"	banner a " .
			"where " .
			"	banner_id = $id";
	
	$dbq = $database->query($sql);
	
	if($database->getNumRows($dbq) > 0) {
		
		$bnr = $database->getArray($dbq);
	
		if($bnr['type_id'] == 3) $html = $bnr['content'];
		else {
			
			if(strtolower(end(explode('.', $bnr['file']))) == 'swf') $html = $config->get('BANNER_MODEL_FLASH');
			else $html = $config->get('BANNER_MODEL_IMAGE');
			
			$src = "{$url}/{$dir}/{$bnr['banner_id']}/{$bnr['file']}";
			
			$width = $bnr['width'];
			$height = $bnr['height'];
			
			if($width == 0 or $height == 0) {
				
				$size = getimagesize($loader->getRootPath() . "/{$dir}/{$bnr['banner_id']}/{$bnr['file']}");
				
				if($width == 0) $width = $size[0];
				if($height == 0) $height = $size[1];
				
			}
			
			$html = str_replace('%W%', $width, $html);
			$html = str_replace('%H%', $height, $html);
			$html = str_replace('%SRC%', $src, $html);
			$html = str_replace('%ALT%', $bnr['title'], $html);
		
		}
		
		$xml->addData("<banner><![CDATA[$html]]></banner>");
	
	}

}

$xml->render();
?>