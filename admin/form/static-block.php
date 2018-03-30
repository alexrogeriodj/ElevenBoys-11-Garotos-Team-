<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$loader->load('Session', 'library');
$session = new Session($loader);

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	$database = $loader->getObject('DataBase', 'database');
	
	if(empty($title)) $valid->addError('title', 'Campo obrigatório.');
	if(empty($content)) $valid->addError('content', 'Campo obrigatório.');
	
	if(empty($name)) $valid->addError('name', 'Campo obrigatório.');
	else {
		$sql = "select 'S' from static_block where name = '$name'" . ($op == 'A' ? " and static_block_id != $id" : '');
		if($database->get($sql) == 'S') $valid->addError('name', 'Identificador indisponível.');
	}
	
	if(!$valid->hasErrors()) {
	
		$block = $loader->getObject('StaticBlock', 'class', false);
		
		$data = $loader->getObject('Model', 'engine', true, false);
		
		$data->set('name',		$name);
		$data->set('title',		$title);
		$data->set('content',	$content);
		$data->set('status',	$status);
		
		if($op == 'A') $data->set('id', $id);
				
		if($op == 'I') {
			
			$result = $block->add($data);
			if($result > 0) 
				$msg = array($result, "Bloco estático '$title' cadastrado com sucesso.");
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			$result = $block->save($data);
			if($result > 0) 
				$msg = array($id, "Bloco estático '$title' atualizado com sucesso.");
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
//			if($dbq) $msg = array($id, 'Bloco estático removido com sucesso.');	
//			else $msg = array(0, 'Erro interno na remoção.');
			
			$msg = array(0, 'Erro interno na remoção.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($valid->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>