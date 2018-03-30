<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$loader->load('Session', 'library');
$session = new Session($loader);

if($session->isLogged()) {

	$database = $loader->getObject('DataBase', 'database');
	$url      = $loader->getObject('Url');
	
	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($title)) $valid->addError('title', 'Campo obrigatório.');
	if(empty($content)) $valid->addError('content', 'Campo obrigatório.');
	
	if(!$valid->hasErrors()) {
	
		$uid = $session->getUserID();
		
		if($op == 'I') {
			
			$id = $database->get('select coalesce(max(page_id), 0) + 1 from page');
			
			$sql = "insert into page(" .
					"	page_id, " .
					"	title, " .
					"	content, " .
					"	type, " .
					"	fixed, " .
					"	account_id, " .
					"	status, " .
					"	date_added, " .
					"	date_modified" .
					") " .
					"values (" .
					"	$id, " .
					"	'$title', " .
					"	'$content', " .
					"	'$type', " .
					"	'N', " .
					"	$uid, " .
					"	$status, " .
					"	now(), " .
					"	now()" .
					")";
			
			$dbq = $database->query($sql);
			
			if($dbq) {
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($title));
						
					$url->saveUrl('page.php?id=' . $id, $url_alias);
				}
				
				$msg = array($id, "Página '$title' cadastrada com sucesso.");
			}
			else $msg = array(0, 'Erro interno no cadastro da página.');
			
		}
		else if($op == 'A') {
			
			$sql = "update " .
					"	page " .
					"set " .
					"	title = '$title', " .
					"	content = '$content', " .
					"	type = '$type', " .
					"	account_id = $uid, " .
					"	status = $status, " .
					"	date_modified = now() " .
					"where " .
					"	page_id = $id";
					
			$dbq = $database->query($sql);
			
			if($dbq) {
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($title));
					
					$url->saveUrl('page.php?id=' . $id, $url_alias);	
				}
				
				$msg = array($id, "Página '$title' atualizada com sucesso.");
			}
			else $msg = array(0, 'Erro interno na atualização da página.');
		
		}
		else if($op == 'E') {
			
			$sql = "delete from page where page_id=$id";
			$dbq = $database->query($sql);
			
			if($dbq) {
				$url->removeUrl('page.php?id=' . $id);
				$msg = array($id, 'Página removida com sucesso.');
			}	
			else $msg = array(0, 'Erro interno na remoção da página.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($valid->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>