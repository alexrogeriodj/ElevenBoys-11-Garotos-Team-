<?php
/**
 * Script para remover as imagens de cache que estão dentro da própria pasta onde foi feito o upload.
 */
require('../core/Loader.php');
$loader = new Loader();

$database = $loader->getObject('DataBase', 'database');
$config   = $loader->get('config');

$config->load('upload');
$updir = $loader->getRootPath() . '/' . $config->get('UP_DIR_IMG');

$sql = "select " .
		"	image_id, " .
		"	name " .
		"from " .
		"	image";

$dbq = $database->query($sql);

while($row = $database->getArray($dbq)) {
	
	$dir = "{$updir}/{$row['image_id']}";
				
	if(is_dir($dir)) {
		
		$read = opendir($dir);
		while($item = readdir($read)) {
			
			if($item == "." or $item == ".." or $item == $row['name']) continue;
			
			if(is_file("{$dir}/{$item}") && !unlink("{$dir}/{$item}"))
				echo '<div style="font-size:14px;color:#555;">' . "{$dir}/{$item}" . ' <strong style="color:#f40;">[fail]</strong></div>';
			else 
				echo '<div style="font-size:14px;color:#555;">' . "{$dir}/{$item}" . ' <strong style="color:#0a0;">[success]</strong></div>';
					
		}
		closedir($read);
			
	}
	
}
?>