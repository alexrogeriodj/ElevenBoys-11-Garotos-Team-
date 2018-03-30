<?php
class DataBase {
	
	private $type;
	private $driver;
	private $error;
	
	function __construct($loader, $base='DEFAULT') {
		
		$config = $loader->get('config');
		$config->load('database');
		$data = $config->get('DB_' . str_replace('DB_', '', $base));
		
		if(is_array($data)) {
			
			$TYPE=null; $HOST=null; $NAME=null; $USER=null; $PASS=null;
			
			extract($data);
			
			$this->type = $TYPE;
			
			if($loader->load($TYPE, 'database')) $this->driver = new $TYPE($HOST, $NAME, $USER, $PASS);
			else $this->error = '[DataBase: Não pode carregar o banco de dados ' . $base . '!]';
			
		}
		else $this->error = '[DataBase: Não pode carregar a configuração ' . $base . '!]';
		
	}
	
	public function isConnected() {
		return $this->driver->isConnected();
	}
	
	public function query($sql, $pager=null) {
		return $this->driver->query($sql, $pager);
	}
	
	public function get($sql) {
		return $this->driver->get($sql);
	}
	
	public function getObject($dbq) {
		return $this->driver->getObject($dbq);
	}
	
	public function getArray($dbq) {
		return $this->driver->getArray($dbq);
	}
	
	public function getRow($dbq) {
		return $this->driver->getRow($dbq);
	}
	
	public function getNumRows($dbq) {
		return $this->driver->getNumRows($dbq);
	}
	
	public function getAffectedRows($dbq) {
		return $this->driver->getAffectedRows($dbq);
	}
	
	public function getNumFields($dbq) {
		return $this->driver->getNumFields($dbq);
	}
	
	public function getFieldName($dbq, $index) {
		return $this->driver->getFieldName($dbq, $index);
	}
	
	public function getFieldType($dbq, $index) {
		return $this->driver->getFieldType($dbq, $index);
	}
	
	public function getError() {
		return $this->driver->getError();
	}
	
	public function begin() {
		return $this->driver->begin();
	}
	
	public function commit() {
		return $this->driver->commit();
	}
	
	public function rollback() {
		return $this->driver->rollback();
	}
	
	public function setDataSeek($dbq, $index) {
		return $this->driver->setDataSeek($dbq, $index);
	}
	
	public function setTransaction($level=null) {
		return $this->driver->setTransaction($level);
	}
	
	public function close() {
		return $this->driver->close();
	}
	
	public function getType() {
		return $this->type;
	}
	
	function __destruct() {
		$this->close();
	}

}
?>