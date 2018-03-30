<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

if($action == 'REMOVE') {
	
	$session = $loader->getObject('Session', 'library');
	
	if($session->isLogged()) {
		
		$error = true;
		
		$database = $loader->getObject('DataBase', 'database');
		
		$sql = "delete from " .
				"	post_image " .
				"where " .
				"	post_id = {$post_id} " .
				"	and image_id = {$id}";
	
		if($database->query($sql)) {
			
			if($loader->getObject('Image', 'library')->removeImage($id)) 
				$error = false; 
			
		}
		
		if(!$error) 
			$xml->addResult(array($id, 'Imagem removida com sucesso.'));
		else
			$xml->addResult(array(0, 'Erro na remoção da imagem.'));
		
	}
	else $xml->addData($session->getMessageXML());
	
}

$xml->render();
?>