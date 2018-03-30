<?php

require('../../core/Loader.php');
$loader = new Loader('XML', 'library');

$xml = new XML();

$session = $loader->getObject('Session', 'library');

if($session->isLogged()) {

	$loader->load('Validation', 'library');
	$validation = new Validation($op != 'E');

	$database = $loader->getObject('DataBase', 'database');

	$username = trim(strtolower($username));
	if(empty($username)) $validation->addError('username', 'Campo obrigatório.');
// 	else if(eregi("[àáâãäçèéêëìíîïñòóôõöùúûüýÿ' '!@#$%¨&*()+,<>;:?/]", $username)) 
// 		$validation->addError('username', 'Contém caracteres inválidos.');
// 	else {
// 		$and = ($op == 'A') ? " and account_id <> $id" : '';
// 		$sql = "select 'S' from account where username = '$username'$and";
// 		if($database->get($sql) == 'S') $validation->addError('username', 'Nome de usuário indisponível.');	
// 	}

	if(!empty($password)) {
		if(strlen($password) < 6) $validation->addError('password', 'A senha deve conter mais de 6 caracteres.');
	}
	else if($op == 'I') $validation->addError('password', 'Campo obrigatório.');
	
	if($password != $confPassword) $validation->addError('confPassword', 'Senhas não conferem.');
	else if($op == 'I') {
		if(empty($confPassword)) $validation->addError('confPassword', 'Campo obrigatório.');
	}
	
	$email = strtolower($email);
	if(empty($email)) $validation->addError('email', 'Campo obrigatório.');
	else if(!$validation->validEmail($email)) $validation->addError('email', 'Email inválido.');
	else {
		$and = ($op == 'A') ? " and account_id <> $id" : '';
		$sql = "select 'S' from account where email = '$email'$and";
		if($database->get($sql) == 'S') $validation->addError('email', 'Este email já está sendo usado por outro usuário.');;	
	}
	
	if(empty($name)) $validation->addError('name', 'Campo obrigatório.');
	
	if(!empty($birthday)) {
		if(!$validation->validDate($birthday)) $validation->addError('birthday', 'Data inválida.');
	}
	

	if(!$validation->hasErrors()) {
		
		$account = $loader->getObject('Account', 'class', false);
		
		$data = new Model();
		
		$data->set('username', $username);
		$data->set('password', $password);
		$data->set('typeId',   $type);
		$data->set('name',     $name);
		$data->set('email',    $email);
		$data->set('phone',    $phone);
		$data->set('birthday', $birthday);
		$data->set('address',  $address);
		$data->set('city',     $city);
		$data->set('stateId',  $state);
		$data->set('status',   $status);
		
		if($op != 'I') $data->set('id', $id);

		if($op == 'I') {
			
			$id = $account->add($data);
			if($id > 0) 
				$msg = array($id, "Usuário '$name' cadastrado com sucesso.");
			else 
				$msg = array(0, 'Erro interno no cadastro do usuário.');
			
		}
		else if($op == 'A') {
			
			if($account->edit($data)) 
				$msg = array($id, "Usuário '$name' atualizado com sucesso.");
			else 
				$msg = array(0, 'Erro interno na atualização do usuário.');
		
		}
		else if($op == 'E') {
			
			if($session->get('userid') != $id) {
				
				if($account->delete($id)) 
					$msg = array($id, 'Usuário removido com sucesso.');	
				else 
					$msg = array(0, 'Erro interno na remoção do usuário (Caso o usuário estiver ' .
							'associado a algum registro no sistema ele não poderá ser excluído).');
			
			}
			else 
				$msg = array(0, 'Você não pode remover sua própria conta. ' .
						'Somente outro usuário administrador pode fazê-la.');
		
		}
		
		$xml->addResult($msg);
	
	}
	else $xml->addData($validation->getValidationXML());

}
else $xml->addData($session->getMessageXML());

$xml->render();
?>