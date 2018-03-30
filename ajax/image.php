<?php
require('../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

if($action == 'SIZE') {
	
	$image = $loader->getObject('Image', 'library');
		
	list($width, $height) = $image->getImageSize($url, $maxw, $maxh);
	
	$xml->addData("<width>$width</width>");
	$xml->addData("<height>$height</height>");
	
}
else if($action == 'GET') {
	
	$image = $loader->getObject('Image', 'library');
	
	$xml->addData('<image><![CDATA[' . $image->getImage($id, $name, $prms) . ']]></image>');
	
}
else if($action == 'SET_CAPTION') {
	
	$session = $loader->getObject('Session', 'library');
	
	if($session->isLogged()) {
	
		$image = $loader->getObject('Image', 'library');
		
		if($image->setCaption($id, $caption)) 
			$xml->addResult(array($id, 'Legenda atualizada com sucesso.'));
		else 
			$xml->addResult(array(0, 'Erro na atualização)'));
		
	}
	else $xml->addData($session->getMessageXML());
	
}
else if($action == 'DELETE') {
	
	$session = $loader->getObject('Session', 'library');
	
	if($session->isLogged()) {
	
		$image = $loader->getObject('Image', 'library');
		
		if($image->removeImage($id)) 
			$xml->addResult(array($id, 'Imagem removida com sucesso.'));
		else 
			$xml->addResult(array(0, 'Erro na remoção da imagem. (Se a imagem estiver associada a algum registro ' .
				'a mesma não poderá ser removida)'));
		
	}
	else $xml->addData($session->getMessageXML());
	
}

$xml->render();
?>