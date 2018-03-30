<?php
final class Session {
	
	private $msgType = null;
	
	private $msgList = array(
		-1 => 'A sessão expirou. Efetue login novamente.',
		0  => 'Acesso negado! Você não possui permissão para realizar essa ação.'
	);
	
	function __construct($loader) {
		session_name($loader->get('config')->get('SESSION_NAME'));
		session_start();
	}
	
	public function get($key) {
		return isset($_SESSION["_$key"]) ? $_SESSION["_$key"] : null;
	}
	
	public function set($key, $value) {
		$_SESSION["_$key"] = $value;
	}
	
	public function has($key) {
		return isset($_SESSION["_$key"]);
	}
	
	public function getUserID() {
		return $this->get('userid');
	}
	
	public function getMessageXML() {
		if(is_null($this->msgType)) return '';
		
		$msg = $this->msgList[$this->msgType];
		return "<result value=\"$this->msgType\"><![CDATA[$msg]]></result>";
	}
	
	public function getMessageType() {
		return $this->msgType;
	}
	
	public function getMessage() {
		return is_null($this->msgType) ? '' : $this->msgList[$this->msgType];
	}
	
	public function setMessageList($msgList) {
		$this->msgList = $msgList;
	}
	
	public function hasAccess($type) {
		if(!$this->isLogged()) return false;
		
		$access = array('ADM' => 1, 'USR' => 2);
		$usrtype = $this->get('usertype');
		
		if($usrtype == 1) return true;
		else if($access[$type] >= $usrtype) return true;
		
		$this->msgType = 0;
		return false;
	}
	
	public function isLogged($redir=false) {
		$this->msgType = null;
		
		if($this->getUserID()) return true;
		else if(!$redir) { 
			$this->msgType = -1; 
			return false; 
		}
		
		$link = end(explode('/', $_SERVER['REQUEST_URI']));
		if(!empty($link)) $link = "?link=$link";
		
		header("Location:login.php$link");
	}
	
}
?>