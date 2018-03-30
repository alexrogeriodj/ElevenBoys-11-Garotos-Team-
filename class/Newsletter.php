<?php
final class Newsletter {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
	}
	
	public function add($data) {
		$database = $this->loader->get('database');
		
		$id = $database->get("select coalesce(max(newsletter_id), 0) + 1 from newsletter");
		$code = $this->generateCode($data->get('email'));
		
		$sql = "insert into newsletter(" .
				"	newsletter_id, " .
				"	email, " .
				"	ip, " .
				"	date_added, " .
				"	code" .
				") " .
				"values (" .
				"	{$id}, " .
				"	lower('" . $data->get('email') . "'), " .
				"	'" . $data->get('ip') . "', " .
				"	now()," .
				"	'{$code}'" .
				")";
		
		if ($database->query($sql))
			return $code;
		
		return false;
	}
	
	public function has($email) {
		$database = $this->loader->get('database');
		
		return (int) $database->get("select 1 from newsletter where lower(email) = lower('{$email}')") == 1;
	}
	
	public function confirm($data, $id) {
		$database = $this->loader->get('database');
		
		$sql = "update newsletter set " .
				"	first_name = '" . $data->get('firstName') . "', " .
				"	last_name = '" . $data->get('lastName') . "', " .
				"	gender = '" . $data->get('gender') . "', " .
				"	birthday = '" . $data->get('birthday') . "', " .
				"	cep = '" . $data->get('cep') . "' " .
				"where newsletter_id = '{$id}'";
		
		return $database->query($sql);
	}
	
	public function getByCode($code) {
		$database = $this->loader->get('database');
		
		$dbq = $database->query("select * from newsletter where code = '{$code}'");
		
		if ($database->getNumRows($dbq) == 1)
			return $database->getObject($dbq);
		
		return false;
	}
	
	private function generateCode($email) {
		return strrev(md5($email));
	}
}
?>
