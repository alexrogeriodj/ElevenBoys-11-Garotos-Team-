<?php
class Model {
	
	private $data = array();
	
	function __get($key) {
		return $this->get($key);
	}
	
	function __set($key, $value) {
		$this->set($key, $value);
	}
	
	public function get($key) {
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	
	public function getIfEmpty($key, $nvl=null, $format=null) {
		$value = isset($this->data[$key]) ? $this->data[$key] : null;
		
		if(empty($value)) {
			if(!is_null($nvl)) $value = $nvl;
		}
		else if(!is_null($format)) $value = str_replace('%', $value, $format);
		
		return $value;
	}

	public function set($key, $value) {
		$this->data[$key] = $value;
		return $this;
	}

	public function has($key) {
    	return isset($this->data[$key]);
  	}
	
}
?>