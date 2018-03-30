<?php
function execute($id, $sid, $loader) {
	
	$database  = $loader->get('database');
	$image     = $loader->get('image');
	
	$oldImg = $database->get("select image_id from post_image where post_id = {$sid} and featured = 1");
	
	$sql = "insert into post_image (" .
			"	post_id, " .
			"	image_id, " .
			"	featured " .
			") " .
			"values (" .
			"	{$sid}, " .
			"	{$id}, " .
			"	1" .
			")";
	
	if($database->query($sql)) {
		
		$sql = "delete from post_image where post_id = {$sid} and image_id = {$oldImg}";
		if($database->query($sql)) 
			$image->removeImage($oldImg);
		
	}
	
	return array();

}
?>