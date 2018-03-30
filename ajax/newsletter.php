<?php
require('../core/Loader.php');
$loader = new Loader();

$valid = $loader->getObject('Validation', 'library', true, false);

if(empty($news_email)) {

	$result = array(
		'error' => 1, 
		'message' => 'Você deve informar um email.'
	);
	
}
else if(!$valid->validEmail($news_email)) {
	
	$result = array(
		'error' => 1, 
		'message' => 'O email informado é inválido.'
	);
	
}	
else {
	$news_email = trim($news_email);
	
	$newsletter = $loader->getObject('Newsletter', 'class', false);
	
	if(!$newsletter->has($news_email)) {
		
		$data = new Model();
		
		$data->set('email', $news_email);
		$data->set('ip', getenv('REMOTE_ADDR'));
		
		if($code = $newsletter->add($data)) {
			
			// Load dos parametros
			$config = $loader->get('config');
			$config->databaseLoad(array(
				'newsletter-subscriber-email-to',
				'newsletter-subscriber-subject',
				'newsletter-confirm-subject',
				'newsletter-success',
				'mailchimp-api-key', 
				'mailchimp-list-id'
			));
			
			// Envio de email de aviso de cadastro
			$objemail = $loader->getObject('Email', 'library');
			
			$objemail->sendMail(
				$config->get('newsletter-subscriber-email-to'), 
				$config->get('newsletter-subscriber-subject'), 
				$objemail->getTemplateMail('newsletter-subscriber', array('EMAIL' => $news_email))
			);
			
			// Envio de email de confirmação de cadastro
			$objemail->sendMail(
				$news_email, 
				$config->get('newsletter-confirm-subject'), 
				$objemail->getTemplateMail('newsletter-confirm', array(
					'CONFIRM_LINK' => $loader->getUrlSite() . '/newsletter?code=' . $code
				))
			);
			
			// Envio do email cadastrado para o MailChimp
			$loader->load('MailChimp', 'class', false);
			$MailChimp = new MailChimp($config->get('mailchimp-api-key'));
			
			$mcResult = $MailChimp->call('lists/subscribe', array(
                'id'                => $config->get('mailchimp-list-id'),
                'email'             => array('email' => $news_email),
                'merge_vars'        => array(),
                'double_optin'      => false,
                'update_existing'   => true,
                'replace_interests' => false,
                'send_welcome'      => false,
            ));
            
            if(isset($mcResult['status']) && $mcResult['status'] == 'error') {
				
				$mcResult['email'] = $news_email;
				$mcResult['ip'] = $ip;
				$mcResult['id'] = $id;
				$mcResult['datetime'] = date('d-m-Y H:i:s');
				
				$objemail->sendMail(
					'webmaster@adorodecorar.com.br', 
					'[Error] MailChimp - ' . $mcResult['name'], 
					'<pre>' . print_r($mcResult, true) . '</pre>'
				);
				
			}
				
			// Mensagem de retorno
			$result = array(
				'error' => 0, 
				'message' => $config->get('newsletter-success')
			);
		
		}
	
		else 
			$result = array(
				'error' => 1, 
				'message' => 'Ocorreu um erro ao cadastrar seu email. Tente novamente mais tarde!'
			);
	
	}
	
	else 
		$result = array(
			'error' => 1, 
			'message' => 'Seu email já está cadastrado!'
		);
	
}	
	
echo json_encode($result);
?>