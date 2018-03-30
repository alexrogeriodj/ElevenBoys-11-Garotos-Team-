<?php
function execute($id, $sid, $loader) {
	
	$database = $loader->get('database');
	
	$sql = "insert into post_image(" .
			"	post_id, " .
			"	image_id, " .
			"	featured" .
			") " .
			"values (" .
			"	$sid, " .
			"	$id, " .
			"	0" .
			")";
			
	if(!$database->query($sql)) return array(0, 'Erro ao associar imagem na galeria.', '');

	return array();
	
}
?>