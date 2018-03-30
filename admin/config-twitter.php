<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin');

$database = $page->get('database');

$page->header('Configuração: Twitter', 'form.css');

$params = array(
	'twitter-app-api-key', 
	'twitter-app-api-secret',
	'twitter-app-access-token',
	'twitter-app-access-token-secret'
);

$page->formIni('config-twitter');

	$page->widgetIni('Configurações - Twitter App');

		$sql = "select " .
				"	* " .
				"from " .
				"	config " .
				"where " .
				"	name in ('" . join("','", $params) . "')";
				
		$dbq = $database->query($sql);
		
		while($row = $database->getArray($dbq)) {
			$page->field('T', $row['description'], 'config[' . $row['name'] . ']', $row['content'], 'CLASS=colfull');	
		}
		
		$page->button(array(
			'VAL=Salvar|SBM'
		));
		
	$page->widgetEnd();
	
$page->formEnd();
	
$page->footer();
?>