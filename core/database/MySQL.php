<?php
final class MySQL {
	
	private $conn;
	
	function __construct($host, $name, $user, $pass) {
		$this->conn = $this->connect($host, $name, $user, $pass);
		
		$this->query("SET NAMES 'utf8'");
		$this->query('SET character_set_connection=utf8');
		$this->query('SET character_set_client=utf8');
		$this->query('SET character_set_results=utf8');
		mysqli_set_charset($this->conn, 'utf8');
	}
	
	private function connect($host, $name, $user, $pass) {
		$conn = mysqli_connect($host, $user, $pass, $name);
		return $conn;
	}
	
	public function query($sql, $pager=null) {
		if($pager) $sql .= " limit $pager[0], $pager[1]";
		return mysqli_query($this->conn, $sql);
	}
	
	public function get($sql) {
		$row = $this->getRow($this->query($sql));
		return $row[0];
	}
	
	public function getObject($dbq) {
		return mysqli_fetch_object($dbq);
	}
	
	public function getArray($dbq) {
		return mysqli_fetch_array($dbq, MYSQLI_ASSOC);
	}
	
	public function getRow($dbq) {
		return mysqli_fetch_row($dbq);
	}
	
	public function getNumRows($dbq) {
		return mysqli_num_rows($dbq);
	}
	
	public function getAffectedRows($dbq) {
		return mysqli_affected_rows($this->conn);
	}
	
	public function getNumFields($dbq) {
		return mysqli_num_fields($dbq);
	}
	
	public function getFieldName($dbq, $index) {
		$data = mysqli_fetch_fields($dbq);
		return $data[$index]->name;
	}
	
	public function getFieldType($dbq, $index) {
		$data = mysqli_fetch_fields($dbq);
		return $data[$index]->type;
	}
	
	public function getError() {
		return mysqli_error($this->conn);
	}
	
	public function begin() {
		return $this->query('BEGIN');
	}
	
	public function commit() {
		return $this->query('COMMIT');
	}
	
	public function rollback() {
		return $this->query('ROLLBACK');
	}
	
	public function setDataSeek($dbq, $index) {
		return mysqli_data_seek($dbq, $index);
	}
	
	public function setTransaction($level=null) {
		switch($level) {
			case 'SERIAL' : $level = "SERIALIZABLE";     break;
			case 'REACOM' : $level = "READ COMMITTED";   break;
			case 'REAUNC' : $level = "READ UNCOMMITTED"; break;
			case 'REPEAT' : $level = "REPEATABLE READ";  break;
			default       : $level = "SERIALIZABLE";     break;
		}
		return $this->query("SET TRANSACTION ISOLATION LEVEL $level");
	}
	
	public function close() {
		return mysqli_close($this->conn);
	}

}
?>