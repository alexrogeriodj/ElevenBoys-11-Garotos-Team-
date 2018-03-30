<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$database = $loader->getObject('DataBase', 'database');
	
	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($title)) $valid->addError('title', ' Campo obrigatório.');
	if(empty($content)) $valid->addError('content', ' Campo obrigatório.');
	
	if($dateIni>$dateFim) $valid->addError('dateFim', ' Preenchimento incorreto');

	if(!$valid->hasErrors()) {
	
		
		$agenda = $loader->getObject('Agenda', 'class', false);
		
		// Dados do post
		$data = new Model();
		$data->set('title',			$title);
		$data->set('content',		$content);
		$data->set('dateIni',		$dateIni);
		$data->set('dateFim',		$dateFim);
		$data->set('room_id',		$room_id);
		$data->set('status',		$status);
		$data->set('userId',		$session->getUserID());
		$data->set('id', 			$op == 'A' ? $id : null);
		
		if($op == 'I') {
			
			$result = $agenda->add($data);
			if($result) {
				$msg = array($result, "'$title' cadastrado com sucesso.");
			}
			else 
				$msg = array(0, 'Já existe um agendamento para esta data e horario.');
			
		}
		else if($op == 'A') {
			
			$result = $agenda->edit($data);
			if($result) {
				$msg = array($id, "'$title' atualizado com sucesso.");
			}
			else 
				$msg = array(0, 'Já existe um agendamento para esta data e horario.');
		
		}
		else if($op == 'E') {
			
			if($agenda->delete($id)) {
				$msg = array($id, 'Removido com sucesso.');
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