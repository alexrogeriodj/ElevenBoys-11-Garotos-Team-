<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($title)) $valid->addError('title', 'Campo obrigatório');
	if(empty($link)) $valid->addError('link', 'Campo obrigatório');
	
	if(!$valid->hasErrors()) {
	
		$banner  = $loader->getObject('Banner', 'class', false);
		
		if($op != 'E') {	
		
			$data = $loader->getObject('Model', 'engine', true, false);
		
			$data->set('formatId', $format_id);
			$data->set('typeId',   $type_id);
			$data->set('link',     $link);
			$data->set('title',    $title);
			$data->set('newTab',   $new_tab);
			$data->set('content',  $content);
			$data->set('width',    0);
			$data->set('height',   0);
			$data->set('status',   $status);
			$data->set('id',       $op == 'A' ? $id : null);
		
		}
		
		if($op == 'I') {
			
			$result = $banner->add($data);
			if($result > 0) 
				$msg = array($result, 'Banner cadastrado com sucesso.');
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			if($banner->edit($data)) 
				$msg = array($id, 'Banner atualizado com sucesso.');
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			if($banner->delete($id)) 
				$msg = array($id, 'Banner removido com sucesso.');
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