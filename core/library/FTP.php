<?php
final class FTP {
	
	private $ftp;
	
	public function connect($host, $user, $pass) {
		$this->ftp = ftp_connect($host);
		
		if($this->ftp)  return ftp_login($this->ftp, $user, $pass);
		
		return false;
	}
	
	public function get($remote, $local, $bin=false) {
		return ftp_get($this->ftp, $local, $remote, $bin ? FTP_BINARY : FTP_ASCII);
	}
	
	public function getContentString($remote, $bin=false) {
		return stream_get_contents($this->getContent($remote, $bin));
	}
	
	public function getContent($remote, $bin=false) {
		$file = fopen('php://temp', 'w+'); 
		
		ftp_fget($this->ftp, $file, $remote, $bin ? FTP_BINARY : FTP_ASCII);
		
		rewind($file);
		
		return $file;
	}
	
	public function put($local, $remote, $bin=false) {
		return ftp_put($this->ftp, $remote, $local, $bin ? FTP_BINARY : FTP_ASCII);
	}
		
	public function putContent($file, $remote, $bin=false) {
		rewind($file);
		return ftp_fput($this->ftp, $remote, $file, $bin ? FTP_BINARY : FTP_ASCII);
	}
	
	public function getDirectoryList($dir='.') {
		return ftp_nlist($this->ftp, $dir);
	}
	
	public function delete($fileName) {
		return ftp_delete($this->ftp, $fileName);
	}
	
	public function getDirectory() {
		return ftp_pwd($this->ftp);
	}
	
	public function setDirectory($dir) {
		return ftp_chdir($this->ftp, $dir);
	}
	
	public function setPermission($fileName, $mode) {
		return ftp_chmod($this->ftp, $mode, $fileName);
	}
	
	function __destruct() {
		ftp_close($this->ftp);
	}
	
}
?>