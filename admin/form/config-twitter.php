<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$_config = $loader->getObject('Config', 'library');
	
	foreach($config as $name => $content) {
		$_config->databaseSave($name, $content);
	} 
			
	$msg = array($id, "Configuração atualizada com sucesso.");
	
	$xml->addResult($msg);

}
else $xml->addData($session->getMessageXML());

$xml->render();
?>