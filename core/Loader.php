<?php
error_reporting(0);
ini_set("display_errors","0");

foreach($_GET as $k => $v) ${$k} = $v;

foreach($_POST as $k => $v) {
	if(is_array($v)) {
		foreach($v as $k2 => $v2) {
			if(is_array($v2)) {
				foreach($v2 as $k3 => $v3) {
					if(is_array($v3)) $v2[$k3] = $v3;
					else $v2[$k3] = trim(str_replace("'", "''", stripslashes($v3)));
				}
				$v[$k2] = $v2;
			}
			else $v[$k2] = trim(str_replace("'", "''", stripslashes($v2)));
		}
		${$k} = $v;
	}
	else ${$k} = trim(str_replace("'", "''", stripslashes($v)));
}

final class Loader {
	
	private $rootPath;
	private $urlSite;
	private $error;
	private $loader = array();

	function __construct($name=null, $dir=null) {
		$this->rootPath = str_replace('\\', '/', realpath(dirname(__FILE__) . '/../'));
		
		$script = str_ireplace($this->rootPath, '', str_replace('\\', '/', $_SERVER['SCRIPT_FILENAME']));
		$this->urlSite = $_SERVER['SERVER_NAME'] . str_replace($script, '', $_SERVER['PHP_SELF']);
		
		if(!file_exists("$this->rootPath/config/database.php")) header('Location:install');
	
		$this->load('Model');
	
		$this->load('Config', 'library');
		$this->set('config', new Config($this));
		
		if($name) $this->load($name, $dir);
	}
	
	function __get($key) {
		return $this->get($key);
	}
	
	function __set($key, $value) {
		$this->set($key, $value);
		return $this;
	}
	
	public function get($key) {
		return (isset($this->loader[$key]) ? $this->loader[$key] : NULL);
	}

	public function set($key, $value) {
		$this->loader[$key] = $value;
		return $this;
	}
	
	public function has($key) {
    	return isset($this->loader[$key]);
  	}

	public function getUrlSite($https=false) {
		return ($https ? 'https://' : 'http://') . $this->urlSite;
	}
	
	public function getRootPath() {
		return $this->rootPath;
	}
	
	public function getObject($name, $dir=null, $core=true, $loader=true) {
		return $this->requireObject($name, $dir, $core, $loader)->get(strtolower($name));
	}
	
	public function requireObject($name, $dir=null, $core=true, $loader=true) {
	
		$key = strtolower($name);
		if(!$this->has($key)) {
			
			$this->load($name, $dir, $core);
			
			$object = $loader ? new $name($this) : new $name();
			$this->set($key, $object);
			
		}
		
		return $this;
	}
	
	public function load($name, $dir=null, $core=true) {
		if(!$dir) $dir='engine';
		
		$file = "$this->rootPath/" . ($core ? 'core/' : '') . "$dir/$name.php";
		
		if(file_exists($file)) require_once($file);
		else $this->error = '[Loader : Não pode carregar a configuração ' . $file . '!]';
		
		return !$this->error;
	}
	
	public function getError() {
		return $this->error;
	}
	
}
?>
