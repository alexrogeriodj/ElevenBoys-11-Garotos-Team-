<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');
	
	if(empty($title)) 
		$validation->addError('title', 'Campo obrigatório.');
	
	if(!$validation->hasErrors()) {
		
		$slider = $loader->getObject('Slider', 'class', false);
		
		$data = new Model();
		
		$data->set('title', 		$title);
		$data->set('link', 			$link);
		$data->set('text', 			$text);
		$data->set('sort_order', 	$sort_order);
		$data->set('account_id', 	$session->getUserID());
		
		if($op == 'I') {
			
			$data->set('slider_id', $slider_id);
			$data->set('status', 	2);
			
			$result = $slider->addItem($data);
			
			if($result) 
				$msg = array($result, "Slider '{$title}' cadastrado com sucesso.");
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			$data->set('status', 	$status);
			$data->set('id', 		$id);
			
			$result = $slider->editItem($data);
			
			if($result) 
				$msg = array($result, "Slider '{$title}' atualizado com sucesso.");
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			$result = $slider->deleteItem($id);
			
			if($result)
				$msg = array($id, 'Slider removido com sucesso.');
			else 
				$msg = array(0, 'Erro interno na remoção.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($vld->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>