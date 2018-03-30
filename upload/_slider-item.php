<?php
function execute($id, $sid, $loader) {
	
	$database = $loader->get('database');
	
	$sql = "select image_id, status from slider_item where slider_item_id = {$sid}";
	$obj = $database->getObject($database->query($sql));
	
	$sta = $obj->status == 2 ? ", status = 1 " : '';
	$sql = "update slider_item set image_id = {$id} {$sta} where slider_item_id = {$sid}";
	
	if($database->query($sql) and !empty($obj->image_id)) 
		$loader->get('image')->removeImage($obj->image_id);
	
	return array();

}
?>