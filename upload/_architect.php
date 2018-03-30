<?php
function execute($id, $sid, $loader) {
	
	$database = $loader->get('database');
	
	$sql = "select image_id from architect where architect_id = {$sid}";
	$imgId = $database->get($sql);
	
	$sql = "update architect set image_id = {$id} where architect_id = {$sid}";
	
	if($database->query($sql))
		$loader->get('image')->removeImage($imgId);
	
	return array();

}
?>