<?php
/**
 * Script de testes com a API do MailChimp.
 */
require('../core/Loader.php');
$loader = new Loader('Page');

// Load dos parametros
$config = $loader->get('config');
$config->databaseLoad(array(
	'newsletter-subscriber-email-to',
	'newsletter-subscriber-subject',
	'newsletter-success',
	'mailchimp-api-key', 
	'mailchimp-list-id'
));

$news_email = 'sergio.rodrigues@universaldecor.com.br';

// Envio de email de aviso de cadastro
$objemail = $loader->getObject('Email', 'library');

//$objemail->sendMail(
//	$config->get('newsletter-subscriber-email-to'), 
//	$config->get('newsletter-subscriber-subject'), 
//	$objemail->getTemplateMail('newsletter', array('EMAIL' => $news_email))
//);

// Envio do email cadastrado para o MailChimp
$loader->load('MailChimp', 'class', false);
$MailChimp = new MailChimp($config->get('mailchimp-api-key'));

$result = $MailChimp->call('lists/subscribe', array(
    'id'                => $config->get('mailchimp-list-id'),
    'email'             => array('email' => $news_email),
    //'merge_vars'        => array('FNAME'=> 'Sergio', 'LNAME'=> 'Rodrigues', 'MERGE4' => '02/04'),
    'merge_vars'        => array(),
    'double_optin'      => true,
    'update_existing'   => true,
    'replace_interests' => false,
    'send_welcome'      => true,
));

echo '<pre>' . print_r($result, true) . '</pre>';

//if(isset($result['status']) && $result['status'] == 'error') {
//	$objemail->sendMail(
//		'sergio.rodrigues@universaldecor.com.br', 
//		'[Error] MailChimp - ' . $result['name'], 
//		'<pre>' . print_r($result, true) . '</pre>'
//	);
//}
?>