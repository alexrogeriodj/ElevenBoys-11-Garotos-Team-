<?php
function execute($id, $sid, $loader) {
	
	$database = $loader->get('database');
	
	$sql = "select image_id, status from banner where banner_id = {$sid}";
	$obj = $database->getObject($database->query($sql));
	
	$sta = $obj->status == 2 ? ", status = 1 " : '';
	$sql = "update banner set image_id = {$id}{$sta} where banner_id = {$sid}";
	
	if($database->query($sql) and !empty($obj->image_id)) 
		$loader->get('image')->removeImage($obj->image_id);
	
	return array();

}
?>