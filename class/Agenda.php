<?php
final class Agenda {
	
	private $loader;
	
	function __construct($loader) {
		$this->loader = $loader;
		$this->loader->requireObject('DataBase', 'database');
		$this->loader->requireObject('Cache', 'library');
		$this->loader->requireObject('Functions', 'library');
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
	
	public function add($data) {
		$database = $this->loader->get('database');

		$dateIni = str_replace('/', '-', $data->dateIni);
		$dateIni = date("Y-m-d H:i:s", strtotime($dateIni));
		
		$dateFim = str_replace('/', '-', $data->dateFim);
		$dateFim = date("Y-m-d H:i:s", strtotime($dateFim)); 
		
		$ferrou= $database->get("select count(*) from schedule where date_ini BETWEEN '$dateIni' AND '$dateFim' and date_fim BETWEEN '$dateIni' AND '$dateFim' and id != '$data->id'");
		
		if($ferrou == "0"){
		
			$id = $database->get('select coalesce(max(id), 0) + 1 from schedule');
			
			$sql = "insert into schedule (" .
					"	id, " .
					"	title, " .
					"	content, " .
					"	account_id, " .
					"	date_ini, " .
					"	date_fim, " .
					"	room_id, " .
					"	status, " .
					"	date_added, " .
					"	date_modified" .
					") " .
					"values (" .
					"	{$id}, " .
					"	'{$data->title}', " .
					"	'{$data->content}', " .
					"	{$data->userId}, " .
					"	'{$dateIni}', " .
					"	'{$dateFim}', " .
					"	{$data->room_id}, " .
					"	{$data->status}, " .
					"	now(), " .
					"	now()" .
					")";
			
			if($database->query($sql)) {
				return (int) $id;
			}
			
			return 0;
		}
		return 0;
	}
	
	public function edit($data) {
		$database = $this->loader->get('database');
		$functions = $this->loader->get('functions');
		
		
		$dateIni = str_replace('/', '-', $data->dateIni);
		$dateIni = date("Y-m-d H:i:s", strtotime($dateIni)); 
		
		$dateFim = str_replace('/', '-', $data->dateFim);
		$dateFim = date("Y-m-d H:i:s", strtotime($dateFim)); 
		
// 		echo $dateIni;
// 		echo $dateFim;
		
		$ferrou= $database->get("select count(*) from schedule where date_ini BETWEEN '$dateIni' AND '$dateFim' and date_fim BETWEEN '$dateIni' AND '$dateFim' and id != '$data->id'");
// 		echo "'select count(*) from schedule where date_ini (BETWEEN '$dateIni' AND '$dateFim') and date_fim (BETWEEN '$dateIni' AND '$dateFim') and id != '$data->id'";
// 		echo $ferrou . "teste";
		
		if($ferrou == "0"){
		
			$sql = "update " .
					"	schedule " .
					"set " .
					"	title = '{$data->title}', " .
					"	content =  '{$data->content}', " .
					"	account_id = {$data->userId}, " .
					"	date_ini = '{$dateIni}', " .
					"	date_fim ='{$dateFim}', " .
					"	room_id = {$data->room_id}, " .
					"	status = {$data->status}, " .
					"	date_modified = now() " .
					"where " .
					"	id = {$data->id}";
			
			if($database->query($sql)) {
				return true;
			}
			
			return false;
		}
		return false;
	}
	
	public function delete($id) {
		$database = $this->loader->get('database');
		
		$sql = "delete from schedule where id = {$id}";
		return $database->query($sql);
	}
	

	
	
	public function buscaDados($room, $dateDB, $turno, $aulas){
		$database = $this->loader->get('database');
		
		$and = "";
		if(isset($room) && !empty($room))
			$and .= " and s.room_id='{$room}'";
			
			$sql = "select " .
					"	s.id, " .
					"	s.title, " .
					"	s.shift_class, " .
					"	a.account_id, " .
					"	a.name " .
					"from " .
					"	schedule s " .
					"	inner join account a " .
					"	on s.account_id=a.account_id " .
					"where " .
					"	date_scheduling='$dateDB' " .
					"	{$and} " ;
			$dbq=$database->query($sql);
			
			$data = array();
			
			$dbq = $database->query($sql, array(0, 1));
			
			if($database->getNumRows($dbq) > 0) {
				$data = $database->getArray($dbq);
			}
			return $data;
			
	}

}
?>