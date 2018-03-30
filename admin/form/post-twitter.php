<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');
$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');
	
	if(empty($twitter_message)) 
		$validation->addError('twitter_message', 'Campo obrigatório.');
	else if(strlen($twitter_message) > 140) 
		$validation->addError('twitter_message', 'O texto deve conter até 140 caracteres.');
	
	if(!$validation->hasErrors()) {
	
		$config = $loader->get('config');
		$config->databaseLoad(array(
			'twitter-app-api-key', 
			'twitter-app-api-secret',
			'twitter-app-access-token',
			'twitter-app-access-token-secret'
		));
	
		$loader->load('codebird', 'class/twitter', false);

		\Codebird\Codebird::setConsumerKey($config->get('twitter-app-api-key'), $config->get('twitter-app-api-secret'));
		
		$cb = \Codebird\Codebird::getInstance();
		$cb->setToken($config->get('twitter-app-access-token'), $config->get('twitter-app-access-token-secret'));
		
		if(isset($imageId) && $imageId > 0) {
			
			$image = $loader->getObject('Image', 'library');	
			$imagePath = $image->getImagePath($imageId, $imageName);
			
			$params = array(
			    'status' => $twitter_message,
			    'media[]' => $imagePath
			);
			
			$reply = $cb->statuses_updateWithMedia($params);
			
			$msg = array(1, 'Post publicado com sucesso.');
				
		}
		else {
			
			$reply = $cb->statuses_update('status=' . $twitter_message);
			
			$msg = array(1, 'Post publicado com sucesso.');
			
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($validation->getValidationXML());

}
else $xml->addData($session->getMessageXML());

$xml->render();
?>