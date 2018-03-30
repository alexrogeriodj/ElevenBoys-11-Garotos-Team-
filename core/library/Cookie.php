<?php
final class Cookie {
	
	private $encryption;
	private $expire;
	
	function __construct($loader, $expire=null) {
		$this->expire = time() + ($expire ? $expire : 60*60*24*30);
		
		$loader->load('Encryption', 'library');
		$this->encryption = new Encryption($loader->get('config')->get('CRYPT_KEY'));
	}
	
	public function get($key) {
		return $this->encryption->decrypt($_COOKIE[$key]);
	}
	
	public function set($key, $value, $expire=null) {
		if(!$expire) $expire = $this->expire;
		setcookie($key, $this->encryption->encrypt($value), $expire, '/');
	}
	
	public function has($key) {
		return isset($_COOKIE[$key]);
	}
	
	public function remove($key) {
		setcookie($key, null, time() - 3600, '/');
	}
	
}
?>