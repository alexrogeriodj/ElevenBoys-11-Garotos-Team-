<?php
final class PgSQL {
	
	private $conn;
	
	function __construct($host, $name, $user, $pass) {
		$this->conn = $this->connect($host, $name, $user, $pass);
	}
	
	private function connect($host, $name, $user, $pass) {
		$conn = pg_connect("host=$host dbname=$name user=$user password=$pass");
		return $conn;
	}
	
	public function isConnected() {
		return $this->conn ? true : false;
	}
	
	public function query($sql, $pager=null) {
		if($pager) $sql .= " offset $pager[0] limit $pager[1]";
		return pg_query($this->conn, $sql);
	}
	
	public function get($sql) {
		$row = $this->getRow($this->query($sql));
		return $row[0];
	}
	
	public function getObject($dbq) {
		return pg_fetch_object($dbq);
	}
	
	public function getArray($dbq) {
		return pg_fetch_array($dbq, null, PGSQL_ASSOC);
	}
	
	public function getRow($dbq) {
		return pg_fetch_row($dbq);
	}
	
	public function getNumRows($dbq) {
		return pg_num_rows($dbq);
	}
	
	public function getAffectedRows($dbq) {
		return pg_affected_rows($dbq);
	}
	
	public function getNumFields($dbq) {
		return pg_num_fields($dbq);
	}
	
	public function getFieldName($dbq, $index) {
		return pg_field_name($dbq, $index);
	}
	
	public function getFieldType($dbq, $index) {
		return pg_field_type($dbq, $index);
	}
	
	public function getError() {
		return pg_last_error($this->conn);
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
		return pg_result_seek($dbq, $index);
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
		return pg_close($this->conn);
	}

}
?>