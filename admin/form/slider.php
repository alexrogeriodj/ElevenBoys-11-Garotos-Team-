<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');
	
	if(empty($identifier)) 
		$validation->addError('identifier', 'Campo obrigatório.');
	
	if(empty($name))
		$validation->addError('name', 'Campo obrigatório.');
	
	if(!$validation->hasErrors()) {
		
		$slider = $loader->getObject('Slider', 'class', false);
		
		$data = new Model();
		
		$data->set('identifier', 	$identifier);
		$data->set('name', 			$name);
		$data->set('status', 		$status);
		$data->set('account_id', 	$session->getUserID());
		
		if($op == 'I') {
			
			$result = $slider->add($data);
			
			if($result) 
				$msg = array($result, "Slider '{$name}' cadastrado com sucesso.");
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			$data->set('id', $id);
			
			$result = $slider->edit($data);
			
			if($result) 
				$msg = array($result, "Slider '{$name}' atualizado com sucesso.");
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			$result = $slider->delete($id);
			
			if($result)
				$msg = array($id, 'Slider removido com sucesso.');
			else 
				$msg = array(0, 'Erro interno na remoção.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($validation->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>