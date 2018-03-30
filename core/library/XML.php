<?php
final class XML {
	
	private $data;
	
	public function addData($data) {
		$this->data .= $data;
		return $this;
	}
	
	public function addResult($result) {
		$this->data .= "<result value=\"$result[0]\"><![CDATA[$result[1]]]></result>";
	}
	
	public function cdata($data) {
		return "<![CDATA[$data]]>";
	}
	
	public function render() {
		
		$gmtDate = gmdate("D, d M Y H:i:s");
		header("Expires: {$gmtDate} GMT");
		header("Last-Modified: {$gmtDate} GMT");
		header("Cache-Control: no-cache, must-revalidate");
		header("Pragma: no-cache");
		header("Content-Type: text/xml; charset=UTF-8", true);
		header("Access-Control-Allow-Origin: *"); 
		
		echo '<?xml version="1.0" encoding="UTF-8"?><xml>' . $this->data . '</xml>';
		
	}
	
}
?>