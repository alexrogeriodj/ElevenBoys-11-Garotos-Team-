<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($name)) $valid->addError('name', 'Campo obrigatório.');
	if(empty($description)) $valid->addError('description', 'Campo obrigatório.');
	
	if(!$valid->hasErrors()) {
	
		$architect = $loader->getObject('Architect', 'class', false);
		$url       = $loader->getObject('Url');
		
		$data = $loader->getObject('Model', 'engine', true, false);
		
		$data->set('name',			$name);
		$data->set('email',			$email);
		$data->set('description',	$description);
		$data->set('phone',			'');
		$data->set('cellphone',		'');
		$data->set('status',		$status);
		
		if($op == 'A') $data->set('id', $id);
				
		if($op == 'I') {
			
			$result = $architect->add($data);
			if($result) {
				
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($name));
					
					$url->saveUrl('architect.php?id=' . $result, $url_alias, 'arquiteto/');
				}
				
				$msg = array($result, "Arquiteto '$name' cadastrado com sucesso.");
				
			}
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			if($architect->edit($data)) {
				
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($name));
					
					$url->saveUrl("architect.php?id={$id}", $url_alias, 'arquiteto/');
				}
				
				$msg = array($id, "Arquiteto '$name' atualizado com sucesso.");
			}
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			if($architect->delete($id)) {
				$url->removeUrl('architect.php?id=' . $id);
				$msg = array($id, 'Arquiteto removido com sucesso.');
			}
			else 
				$msg = array(0, 'Erro interno na remoção.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($valid->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>