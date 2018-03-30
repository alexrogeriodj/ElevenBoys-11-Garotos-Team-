<?php
require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$database = $loader->getObject('DataBase', 'database');
	
	$loader->load('Validation', 'library');
	$valid = new Validation($op != 'E');
	
	if(empty($title)) $valid->addError('title', 'Campo obrigatório.');
	if(empty($content)) $valid->addError('content', 'Campo obrigatório.');
	
	if(!$valid->hasErrors()) {
	
		$post = $loader->getObject('Post', 'class', false);
		$url  = $loader->getObject('Url');
		$seo  = $loader->getObject('Seo');
		
		// Categorias
		$_categories = array();
		foreach($category as $k => $v) {
			if($v == 'S') $_categories[] = $k;
		}
		
		// Dados do post
		$data = new Model();
		$data->set('title',			$title);
		$data->set('content',		$content);
		$data->set('status',		$status);
		$data->set('categories',	$_categories);
		$data->set('architectId',	$architect_id);
		$data->set('userId',		$session->getUserID());
		$data->set('id', 			$op == 'A' ? $id : null);
		
		$seoData = new Model();
		$seoData->set('title',			empty($seo_title) ? $title : $seo_title);
		$seoData->set('description',	$seo_description);
		$seoData->set('keywords',		$seo_keywords);
		$seoData->set('robots',			'');
				
		if($op == 'I') {
			
			$result = $post->add($data);
			if($result) {
				
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($title));
					
					 $url->saveUrl('post.php?id=' . $result, $url_alias);
				}
				
				$seo->save('post', 'id=' . $result, $seoData);
				
				$msg = array($result, "Post '$title' cadastrado com sucesso.");
			}
			else 
				$msg = array(0, 'Erro interno no cadastro.');
			
		}
		else if($op == 'A') {
			
			$result = $post->edit($data);
			if($result) {
				if($url->rewriteIsOn()) {
					if(empty($url_alias)) 
						$url_alias = $url->makeAlias($url->clearUrl($title));
						
					$url->saveUrl('post.php?id=' . $id, $url_alias);
				}
				
				$seo->save('post', 'id=' . $id, $seoData);
				
				$image = $loader->getObject('Image', 'library');
				
				foreach($image_caption as $k => $v) {
					$image->setCaption($k, $v);
				}
				
				$msg = array($id, "Post '$title' atualizado com sucesso.");
			}
			else 
				$msg = array(0, 'Erro interno na atualização.');
		
		}
		else if($op == 'E') {
			
			if($post->delete($id)) {
				$url->removeUrl('post.php?id=' . $id);
				$msg = array($id, 'Post removido com sucesso.');
			}	
			else 
				$msg = array(0, 'Erro interno na remoção.');
		
		}
		
		$xml->addResult($msg);
		
	}
	else $xml->addData($valid->getValidationXML());
	
}
else $xml->addData($session->getMessageXML());

$xml->render();
?>