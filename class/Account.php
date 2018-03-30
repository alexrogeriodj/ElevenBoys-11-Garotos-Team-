<?php
final class Account {
	
	private $loader;
	private $id;
	private $type;
	private $name;
	private $email;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Functions', 'library');
		$this->loader->requireObject('Session', 'library');
		$this->loader->requireObject('Cookie', 'library');
		
		if($this->isLogged()) {
			$session = $this->loader->get('session');
			$cookie  = $this->loader->get('cookie');
			
			//SE OPÇÃO "MANTER CONECTADO" ESTAVA ATIVADO
			if ($cookie->has('userid')) {
				
				//NESSE CASO O USUÁRIO ESTÁ LOGADO NO SITE ATRAVÉS DA OPÇÃO "MANTER CONECTADO"
				$this->id    = $cookie->get('userid');
				$this->type  = $cookie->get('usertype');
				$this->name  = $cookie->get('name');
				$this->email = $cookie->get('email');
				
				//SE NÃO EXISTE UMA SESSÃO, INICIA UMA
				if (!$session->has('userid')) {
					
					//INICIA VALORES DA SESSÃO
					$session->set('userid',   $this->id);
					$session->set('usertype', $this->type);
					$session->set('name',     $this->name);
					$session->set('email',    $this->email);
					
				}
			}
			else {
				//OPÇÃO "MANTER CONECTADO" NÃO ESTAVA ATIVADO
				//NESSE CASO O USUÁRIO FEZ LOGIN E ESTÁ NAVEGANDO NO SITE APENAS COM A SESSÃO
				$this->id    = $session->get('userid');
				$this->type  = $session->get('usertype');
				$this->name  = $session->get('name');
				$this->email = $session->get('email');
			}
			
		}
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getEmail() {
		return $this->email;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function loginRedirect($folder='') { 
		header('Location:' . $this->loader->getUrlSite() . $folder . '/login');
	}
	
	public function isLogged() {
		$session = $this->loader->get('session');
		$cookie = $this->loader->get('cookie');
		return ($session->has('userid') || $cookie->has('userid')); 
	}
	
	public function password($password) {
		return strrev(md5($password));
	}
	
	public function login($username, $password, $keepConnected=false) {
		$database  = $this->loader->get('database');
		
		$sql = "select " .
				"	account_id, " .
				"	account_type_id, " .
				"	name, " .
				"	email " .
				"from " .
				"	account " .
				"where " .
				"	lower(username) = lower('$username') " .
				"	and password = '" . $this->password($password) ."' " .
				"	and status = 1";
		
		$dbq = $database->query($sql);
		
		if($dbq) {
		
			if($database->getNumRows($dbq) > 0) {
				
				$login = $database->getArray($dbq);
				
				$session = $this->loader->get('session');
				
				$session->set('userid',    $login['account_id']);
				$session->set('usertype',  $login['account_type_id']);
				$session->set('name',      $login['name']);
				$session->set('email',     $login['email']);
				
				if ($keepConnected) {
					$cookie = $this->loader->get('cookie');
				
					$cookie->set('userid',    $login['account_id']);
					$cookie->set('usertype',  $login['account_type_id']);
					$cookie->set('name',      $login['name']);
					$cookie->set('email',     $login['email']);
				}
				
				//$this->editLastLogin($login->account_id);
				
				return $login;
				
			}
		
		}
		
		return null;
	}
	
	public function logout() {
		$session = $this->loader->get('session');
		$cookie  = $this->loader->get('cookie');
		
		if ($cookie->has('userid')) {
			$cookie->remove('userid');
			$cookie->remove('usertype');
			$cookie->remove('name');
			$cookie->remove('email');
		}
		
		session_unset();
		session_destroy();
	}
	
	public function userExists($username, $accountId=null) {
		$database = $this->loader->get('database');
		
		$sql = "select 1 from account where username = '{$username}'";
		if(!empty($accountId)) $sql .= " and account_id != {$accountId}";
		
		return $database->get($sql) == 1;
	}
	
	public function emailExists($email, $accountId=null) {
		$database = $this->loader->get('database');
		
		$sql = "select 1 from account where email = '{$email}'";
		if(!empty($accountId)) $sql .= " and account_id != {$accountId}";
		
		return $database->get($sql) == 1;
	}
	
	public function add($data) {
		$database  = $this->loader->get('database');
		$functions = $this->loader->get('functions');
		
		
		$password = $this->password($data->password);
		$birthday = $functions->formatDate2($data->birthday, 'SQL|IFEMPTY=null');
		
		$id = $database->get('select coalesce(max(account_id), 0) + 1 from account');
			
		$sql = "insert into account (" .
				"	account_id, " .
				"	username, " .
				"	password, " .
				"	account_type_id, " .
				"	name, " .
				"	email, " .
				"	phone, " .
				"	birthday, " .
				"	address, " .
				"	city, " .
				"	state_id, " .
				"	status, " .
				"	date_added, " .
				"	date_modified" .
				") " .
				"values (" .
				"	{$id}, " .
				"	'{$data->username}', " .
				"	'{$password}', " .
				"	{$data->typeId}, " .
				"	'{$data->name}', " .
				"	'{$data->email}', " .
				"	'{$data->phone}', " .
				"	{$birthday}, " .
				"	'{$data->address}', " .
				"	'{$data->city}', " .
				"	'{$data->stateId}', " .
				"	{$data->status}, " .
				"	now(), " .
				"	now() " .
				")";
	
		return $database->query($sql) ? (int) $id : 0;		
	}
	
	public function edit($data) {
		$database  = $this->loader->get('database');
		$functions = $this->loader->get('functions');
		$birthday = $functions->formatDate2($data->birthday, 'SQL|IFEMPTY=null');
		
		$password = $data->password;
		$password = empty($password) ? '' : " password = '" . $this->password($password) . "', ";

		$sql = "update " .
				"	account " .
				"set " .
				"	username 		= '{$data->username}', " .
				"	{$password} " .
				"	account_type_id = {$data->typeId}, " .
				"	name 			= '{$data->name}', " .
				"	email 			= '{$data->email}', " .
				"	phone 			= '{$data->phone}', " .
				"	birthday 		= {$birthday}, " .
				"	address 		= '{$data->address}', " .
				"	city 			= '{$data->city}', " .
				"	state_id 		= '{$data->stateId}', " .
				"	status 			= {$data->status}, " .
				"	date_modified	= now() " .
				"where " .
				"	account_id 		= {$data->id}";

		return $database->query($sql);
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from account where account_id = {$id}";
		
		return $database->query($sql);
	}
	
}
?>