<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$loader->load('Validation', 'library');
$validation = new Validation();

if(empty($user)) $validation->addError('user', 'Campo obrigatório.');
if(empty($pass)) $validation->addError('pass', 'Campo obrigatório.');

if(!$validation->hasErrors()) {
	
	$account = $loader->getObject('Account', 'class', false);
	
	$login = $account->login($user, $pass, isset($keepConnected));
	
	if(!is_null($login)) 
		$msg = array(1, 'Login efetuado com sucesso.', $link);
	else 
		$msg = array(0, 'Dados de login incorretos.');

	$link = isset($msg[2]) ? " link=\"$link\"" : '';
	$xml->addData("<result value=\"$msg[0]\"$link><![CDATA[$msg[1]]]></result>");
	
}
else $xml->addData($validation->getValidationXML());

$xml->render();
?>