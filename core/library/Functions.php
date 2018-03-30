<?php
class Functions {

	private $loader;
	private $rootPath;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->rootPath = $this->loader->getRootPath();
	}
	
	public function arrayGet($key, $array, $ifEmpty='') {
		return isset($array[$key]) ? $array[$key] : $ifEmpty;
	}
	
	function getNameMonth($month, $shorten=false) {
		$monthName[1]  = "Janeiro";  
		$monthName[2]  = "Fevereiro";
		$monthName[3]  = "Março";    
		$monthName[4]  = "Abril";    
		$monthName[5]  = "Maio";     
		$monthName[6]  = "Junho";    
		$monthName[7]  = "Julho";    
		$monthName[8]  = "Agosto";   
		$monthName[9]  = "Setembro"; 
		$monthName[10] = "Outubro";  
		$monthName[11] = "Novembro"; 
		$monthName[12] = "Dezembro"; 
		
		return $shorten ? substr($monthName[$month], 0, 3) : $monthName[$month];
	}
	
	public function mask($val, $mask, $clear=true) {
		
		$maskared = '';
		
		if($clear) $val = ereg_replace("[' '-./ t]", '', $val);
		
		$k = 0;
		for($i = 0; $i<=strlen($mask)-1; $i++) {
			if($mask[$i] == '#') {
				if(isset($val[$k])) $maskared .= $val[$k++];
			}
			else {
				if(isset($mask[$i])) $maskared .= $mask[$i];
			}
		}
		
		return $maskared;
		
	}
	
	public function clearString($value) {
		
		$a = utf8_decode('áàãâéêíóôõúüçñÁÀÃÂÉÊÍÓÔÕÚÜÇÑ ');
		$b = utf8_decode('aaaaeeiooouucnAAAAEEIOOOUUCN_');
		$c = '[^a-zA-Z0-9_.-]';
		
		return ereg_replace($c, '', utf8_encode(strtr(utf8_decode($value), $a, $b)));
		
	}
	
	public function getParam($name) {
		
		$this->loader->requireObject('DataBase', 'database');
		
		$sql = "select content from config where name = '{$name}'";
		return $this->loader->get('database')->get($sql);
		
	}
	
	public function getStatus($sta) {
		$sta = (string) $sta;
		switch ($sta) {
			case '1':
			case 'A':
				$sta = 'Ativo';
				break;
			case '0':
			case 'I':
				$sta = 'Inativo'; 
				break;
			case '2':
			case 'P':
				$sta = 'Pendente'; 
				break;
		}
		return $sta; 
	}

	public function cutText($text, $size, $cont='...') {
		if(strlen(utf8_decode($text)) > $size) 
			$text = utf8_encode(substr(utf8_decode($text), 0, $size)) . $cont;
		return $text;
	}
	
	public function formatDate($date, $format='d \d\e F \d\e Y') {
		if(empty($date)) return '';
		
		$functions = $this->loader->get('functions');
		
		$dateTs = strtotime($date);
		
		$format = str_replace('F', '$1', $format);
		$format = str_replace('M', '$2', $format);
		
		$format = date($format, $dateTs);
		
		$format = str_replace('$1', $functions->getNameMonth(date('n', $dateTs)), $format);
		$format = str_replace('$2', $functions->getNameMonth(date('n', $dateTs), true), $format);
		
		return $format;
	}
	
	public function formatDate2($date, $prm='') {
		
		$prms = explode('|', $prm);
		foreach($prms as $p) {
			$p = explode('=', $p, 2);
			if(count($p) > 1) ${$p[0]} = $p[1];
			else ${$p[0]} = true;
		}
		
		if(!empty($date)) {
			
			$format = $date;
			
			$format = substr($format, 0, 10);
			$format = str_replace('-', '/', $format);
			
			$d = explode('/', $format);
			
			if(isset($SQL)) $format = "$d[2]-$d[1]-$d[0]";
			else {
				$sep = isset($SEPARATOR) ? $SEPARATOR : '/';
				$format = "$d[2]$sep$d[1]$sep$d[0]";
			}
			
			if(isset($TIME)) {
				$length = 8;
				if(!is_bool($TIME)) {
					if($TIME == 'HM') $length = 5;
				}
				$format .= ' ' . substr($date, 11, $length);
			}
			
			if(isset($SQL) and isset($IFEMPTY)) $format = "'$format'";
			
			$date = $format;
		}
		else if(isset($IFEMPTY)) $date = $IFEMPTY;
		
		return $date;
	}
	
}
?>