<?php
final class MsSQL {
	
	private $conn;
	
	function __construct($host, $name, $user, $pass) {
		$this->conn = $this->connect($host, $name, $user, $pass);
		
		$this->query("SET NAMES 'utf8'");
		$this->query("SET CHARACTER SET utf8");
		$this->query("SET CHARACTER_SET_CONNECTION=utf8");
	}
	
	private function connect($host, $name, $user, $pass) {
		$conn = mssql_connect($host, $user, $pass);
		if(!mssql_select_db($name, $conn)) $conn = null;
		return $conn;
	}
	
	public function isConnected() {
		return $this->conn ? true : false;
	}
	
	public function query($sql, $pager=null) {
		if($pager) $sql .= " offset $pager[0] limit $pager[1]";
		return mssql_query($sql, $this->conn);
	}
	
	public function get($sql) {
		$row = $this->getRow($this->query($sql));
		return $row[0];
	}
	
	public function getObject($dbq) {
		return mssql_fetch_object($dbq);
	}
	
	public function getArray($dbq) {
		return mssql_fetch_array($dbq);
	}
	
	public function getRow($dbq) {
		return mssql_fetch_row($dbq);
	}
	
	public function getNumRows($dbq) {
		return mssql_num_rows($dbq);
	}
	
	public function getAffectedRows($dbq) {
		return mssql_rows_affected($dbq);
	}
	
	public function getNumFields($dbq) {
		return mssql_num_fields($dbq);
	}
	
	public function getFieldName($dbq, $index) {
		return mssql_field_name($dbq, $index);
	}
	
	public function getFieldType($dbq, $index) {
		return mssql_field_type($dbq, $index);
	}
	
	public function getError() {
		return mssql_get_last_message();
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
		return mssql_data_seek($dbq, $index);
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
		return mssql_close($this->conn);
	}

}
?>