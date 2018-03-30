<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');
	
	if(empty($name)) $validation->addError('name', 'Campo obrigatório.');
	if(empty($host)) $validation->addError('host', 'Campo obrigatório.');
	if(empty($port)) $validation->addError('port', 'Campo obrigatório.');
	
	if($authenticate == 'S') {
		if(empty($username)) $validation->addError('username', 'Campo obrigatório.');
		if(empty($password)) $validation->addError('password', 'Campo obrigatório.');
	}
	
	if(empty($fromEmail)) $validation->addError('fromEmail', 'Campo obrigatório.');
	else if(!$validation->validEmail($fromEmail)) $validation->addError('fromEmail', 'Email inválido.');
	
	if(empty($fromName)) $validation->addError('fromName', 'Campo obrigatório.');
	
	if(!$validation->hasErrors()) {
		
		$database = $loader->getObject('DataBase', 'database');
			
		$uid = $session->getUserID();
		
		if($op == 'I') {
			
			$id = $database->get('select coalesce(max(config_mail_id), 0) + 1 from config_mail');
			
			$sql = "insert into config_mail(" .
					"	config_mail_id, " .
					"	name, " .
					"	mailer, " .
					"	authenticate, " .
					"	charset, " .
					"	port, " .
					"	security, " .
					"	host, " .
					"	username, " .
					"	password, " .
					"	from_email, " .
					"	from_name, " .
					"	account_id, " .
					"	date_added, " .
					"	date_modified" .
					") " .
					"values (" .
					"	$id, " .
					"	'$name', " .
					"	'$mailer', " .
					"	'$authenticate', " .
					"	'$charset', " .
					"	$port, " .
					"	'$security', " .
					"	'$host', " .
					"	'$username', " .
					"	'$password', " .
					"	'$fromEmail', " .
					"	'$fromName', " .
					"	$uid, " .
					"	now(), " .
					"	now()" .
					")";
			$dbq = $database->query($sql);
			
			if($dbq) $msg = array($id, "Configuração de email cadastrada com sucesso.");
			else $msg = array(0, 'Erro interno no cadastro da configuração de email.');
			
		}
		else if($op == 'A') {
			
			$sql = "update " .
					"	config_mail " .
					"set " .
					"	name = '$name', " .
					"	mailer = '$mailer', " .
					"	authenticate = '$authenticate', " .
					"	charset = '$charset', " .
					"	port = $port, " .
					"	security = '$security', " .
					"	host = '$host', " .
					"	username = '$username', " .
					"	password = '$password', " .
					"	from_email = '$fromEmail', " .
					"	from_name = '$fromName', " .
					"	account_id = $uid, " .
					"	date_modified = now() " .
					"where " .
					"	config_mail_id = $id";
			$dbq = $database->query($sql);
			
			if($dbq) $msg = array($id, "Configuração de email atualizada com sucesso.");
			else $msg = array(0, 'Erro interno na atualização da configuração de email.');
		
		}
		else if($op == 'E') {
			
			$sql = "delete from config_mail where config_mail_id = {$id}";
			$dbq = $database->query($sql);
			
			if($dbq) $msg = array($id, 'Configuração de email removida com sucesso.');
			else $msg = array(0, 'Erro interno na remoção da configuração de email.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($validation->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>