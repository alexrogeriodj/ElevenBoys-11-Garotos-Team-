<?php
require('../../core/Loader.php');
$loader = new Loader();

$data = array('result' => null);

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {
	
	if($session->hasAccess('ADM')) {
	
		$fileFn = $loader->getObject('FileFunctions', 'library');
		
		if($fileFn->deleteFiles($loader->getRootPath() . '/cache')) 
			$data['result'] = array('type' => 1, 'message' => 'Limpeza de cache efetuada com sucesso!');
		else 
			$data['result'] = array('type' => 0, 'message' => 'Aconteceu um erro ao limpar o cache!');
		
	}
	else 
		$data['result'] = array('type' => -1, 'message' => 'Acesso negado! Você não possui permissão para realizar essa ação.!');
}
else 
	$data['result'] = array('type' => -1, 'message' => 'A sessão expirou. Efetue login novamente.');

echo json_encode($data);
?>