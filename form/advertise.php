<?php
require('../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$loader->load('Validation', 'library');
$vld = new Validation();

if(empty($name)) $vld->addError('name', 'Campo obrigatório.');

if(empty($email)) $vld->addError('email', 'Campo obrigatório.');
else if(!$vld->validEmail($email)) $vld->addError('email', 'Email inválido.');

if(empty($message)) $vld->addError('subject', 'Campo obrigatório.');

if(empty($message)) $vld->addError('message', 'Campo obrigatório.');

if(!$vld->hasErrors()) {
	
	$objemail = $loader->getObject('Email', 'library');
	
	$config = $loader->get('config');
	$config->databaseLoad(array(
		'advertise-email-to',
		'advertise-subject',
		'advertise-success'
	));
	
	$data = array(
		'NAME'    => $name,
		'EMAIL'   => $email,
		'SUBJECT' => $subject,
		'MESSAGE' => $message
	);
	
	$content = $objemail->getTemplateMail('advertise', $data);
	
	if($objemail->sendMail($config->get('advertise-email-to'), $config->get('advertise-subject'), $content)) 
		$msg = array(1,  $config->get('advertise-success'));
	else 
		$msg = array(0, 'Erro interno no envio dos dados.');
		
	$xml->addResult($msg);
	
}
else $xml->addData($vld->getValidationXML());

$xml->render();
?>