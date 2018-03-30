<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$database = $loader->getObject('DataBase', 'database');
	
	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($name)) $valid->addError('name', 'Campo obrigatório.');
	
	if(!$valid->hasErrors()) {
	
		$category = $loader->getObject('Category', 'class', false);
		$url      = $loader->getObject('Url');
		
		$data = $loader->getObject('Model', 'engine', true, false);
		
		$data->set('name',		$name);
		$data->set('status',	$status);
		
		if($op == 'A') $data->set('id', $id);
				
		if($op == 'I') {
			
			$result = $category->add($data);
			if($result) {
				
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($name));
					
					$url->saveUrl('category.php?id=' . $result, $url_alias, 'categoria/');
				}
				
				$msg = array($result, "Categoria '$name' cadastrada com sucesso.");
				
			}
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			if($category->edit($data)) {
				
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($name));
					
					$url->saveUrl("category.php?id={$id}", $url_alias, 'categoria/');
				}
				
				$msg = array($id, "Categoria '$name' atualizada com sucesso.");
			}
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			if($category->delete($id)) {
				$url->removeUrl('category.php?id=' . $id);
				$msg = array($id, 'Categoria removida com sucesso.');
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