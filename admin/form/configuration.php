<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');
	
	if(empty($description)) $validation->addError('description', 'Campo obrigatório.');
	if(empty($content)) $validation->addError('content', 'Campo obrigatório.');
	
	if(!$validation->hasErrors()) {
	
		$config = $loader->getObject('Config', 'library');
		
		if($op == 'I') {
			
			$data = new Model();
		
			$data->set('name'       , $name);
			$data->set('description', $description);
			$data->set('helpInfo'   , $help_info);
			$data->set('typeId'     , $type);
			$data->set('content'    , $content);
			$data->set('sortOrder'  , $sort_order);
			$data->set('userId'     , $session->getUserID());
			
			$id = $config->databaseAdd($data);
			
			if($id > 0) 
				$msg = array($id, "Ítem de configuração '$description' cadastrado com sucesso.");
			else 
				$msg = array(0, 'Erro interno no cadastro do ítem de configuração.');
			
		}
		else if($op == 'A') {
			
			if($config->databaseSave($name, $content)) 
				$msg = array($id, "Ítem de configuração '$description' atualizado com sucesso.");
			else 
				$msg = array(0, 'Erro interno na atualização do ítem de configuração.');
		
		}
//		else if($op == 'E') {
//			
//			$sql = "delete from config where config_id = $id";
//			$dbq = $database->query($sql);
//			
//			if($dbq) $msg = array($id, 'Ítem de configuração removido com sucesso.');	
//			else $msg = array(0, 'Erro interno na remoção do ítem de configuração.');
//		
//		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($validation->getValidationXML());

}
else $xml->addData($session->getMessageXML());

$xml->render();
?>