<?php
require('../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$loader->load('Validation', 'library');
$vld = new Validation();

if (!isset($code) || !$code) {
	$msg = array(0, 'Não foi possível encontrar sua assinatura de newsletter.<br />Tente novamente mais tarde.');
	$xml->addResult($msg);
}
else {
	$firstName = trim($firstName);
	$lastName = trim($lastName);
	$birthday = trim($birthday);
	$cep = trim($cep);
	
	if (empty($firstName))
		$vld->addError('firstName', 'Campo obrigatório.');
	
	if (empty($lastName))
		$vld->addError('lastName', 'Campo obrigatório.');
	
	if (empty($birthday))
		$vld->addError('birthday', 'Campo obrigatório.');
	else {
		$updateBirthday = $birthday . '/1970';
		
		if (!$vld->validDate($updateBirthday))
			$vld->addError('birthday', 'Data inválida');
	}
	
	if (empty($cep))
		$vld->addError('cep', 'Campo obrigatório.');
	else if (!$vld->validCEP($cep))
		$vld->addError('cep', 'CEP inválido');
	
	if (!isset($gender))
		$vld->addError('gender', 'Campo obrigatório.');
	
	if (!$vld->hasErrors()) {
		
		$objNewsletter	= $loader->getObject('Newsletter', 'class', false);
		
		if ($newsletter = $objNewsletter->getByCode($code)) {
			
			$updateBirthday	= $loader->getObject('Functions', 'library')->formatDate($updateBirthday, 'SQL');
			
			$config	= $loader->get('config');
			
			$config->databaseLoad(array(
				'newsletter-confirm-success',
				'mailchimp-api-key', 
				'mailchimp-list-id'
			));
			
			$data = new Model();
			
			$data->set('firstName', $firstName);
			$data->set('lastName', $lastName);
			$data->set('birthday', $updateBirthday);
			$data->set('cep', $cep);
			$data->set('gender', $gender);
			
			if ($objNewsletter->confirm($data, $newsletter->newsletter_id)) {
				
				// Envio dos dados para o Mailchimp
				$loader->load('MailChimp', 'class', false);
				$MailChimp = new MailChimp($config->get('mailchimp-api-key'));
				
				$mcResult = $MailChimp->call('lists/subscribe', array(
	                'id'                => $config->get('mailchimp-list-id'),
	                'email'             => array('email' => $email),
	                'merge_vars'        => array(
	                	'FNAME'		=> $firstName,
	                	'LNAME'		=> $lastName,
	                	'BIRTHDAY'	=> $birthday,
	                	'CEP'		=> $cep,
	                	'GENDER'	=> $gender
	                ),
	                'double_optin'      => false,
	                'update_existing'   => true,
	                'replace_interests' => false,
	                'send_welcome'      => false,
	            ));
	            
	            if(isset($mcResult['status']) && $mcResult['status'] == 'error') {
					
					$objemail = $loader->getObject('Email', 'library');
					
					$mcResult['email'] = $email;
					$mcResult['ip'] = $ip;
					$mcResult['id'] = $id;
					$mcResult['datetime'] = date('d-m-Y H:i:s');
					
					$objemail->sendMail(
						'webmaster@universaldecor.com.br', 
						'[Error] MailChimp - ' . $mcResult['name'], 
						'<pre>' . print_r($mcResult, true) . '</pre>'
					);
				}
				else {
					$msg = array(1, $config->get('newsletter-confirm-success'));
					$xml->addResult($msg);
				}
			}
			else {
				$msg = array(0, 'Erro ao confirmar assinatura de newsletter.<br />Tente novamente mais tarde.');
				$xml->addResult($msg);
			}
		}
		else {
			$msg = array(0, 'Mais de uma assinatura de newsletter encontrada com esse código.<br />Entre em contato conosco.');
			$xml->addResult($msg);
		}
	}
	else $xml->addData($vld->getValidationXML());
}

$xml->render();
?>