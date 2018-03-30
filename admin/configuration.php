<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin');

$database = $page->database;

$page->header('Configuração', 'form.css', null, array('pageTitle' => 'Configurações'));

if(!isset($op)) {
	
	$page->tableIni();
	
		$page->lineIni('HEAD');
			$page->cell('Título', 'W=45%');
			$page->cell('Valor', 'W=40%');
			$page->cell('Tipo', 'W=15%');
		$page->lineEnd();
		
		$sqlpag = "select count(config_id) from config";
		$pager = $page->pager($database->get($sqlpag), 'configuration.php', "NRP=30");
		
		$sql = "select " .
				"	config_id, " .
				"	name, " .
				"	description, " .
				"	config_type_id, " .
				"	content " .
				"from " .
				"	config " .
				"order by " .
				"	description";
				
		$dbq = $database->query($sql, $pager);
		while($row = $database->getObject($dbq)) {
			$page->lineIni("LINK=configuration.php?op=A&amp;id=$row->config_id");
				$page->cell($row->description);
				
				if($row->config_type_id == 99) $row->content = str_pad('', strlen($row->content), '*');
				$page->cell($row->content);
				
				$page->cell($row->config_type_id);
			$page->lineEnd();
		}
		
	$page->tableEnd();
	
	echo $page->getPager();
	
}
else if($op == 'I' or $op=='A') {
	
	if($op=='A') {
		$sql = "select * from config where config_id = $id";
		$obj = $database->getObject($database->query($sql));
	}
	
	$page->formIni('configuration');
		$page->field('T', 'Título', 'description', $obj->description, 'W=100|ML=150|RO|CLASS=fw100');
		
//		if($obj->type == 'T') 
			$page->field('T', 'Valor', 'content', $obj->content, "W=100|FOCUS|CLASS=fw100");
//		else if($obj->type == 'A')
//			$page->field('A', 'Valor', 'content', $obj->content, "COLS=80|ROWS=5|FOCUS|CLASS=fw100");
//		else if($obj->type == 'P')
//			$page->field('P', 'Valor', 'content', $obj->content, "W=40|FOCUS|CLASS=fw100");
//		else if($obj->type == 'N')
//			$page->field('T', 'Valor', 'content', $obj->content, "W=20|MASK=integer|FOCUS|CLASS=fw100");
//		else if($obj->type == 'H')
//			$page->field('T', 'Valor', 'content', $obj->content, "W=60|FOCUS|CLASS=fw100");
		
		$page->field('T', 'Nome', 'key-ro', $obj->name, 'RO|CLASS=colset2');
		$page->field('T', 'Tipo', 'type-ro', $obj->config_type_id, 'RO|CLASS=colset2 col2');
		
		$page->field('H', '', 'name', $obj->name);
		$page->field('H', '', 'id', $obj->config_id);
		$page->field('H', '', 'type', $obj->config_type_id);
	
		echo '<div class="clear"></div>';
	
		$page->button(array(
			'VAL=Voltar|LINK=configuration.php',
			'VAL=Salvar|SBM'
		));
	$page->formEnd();
	
}

$page->footer();
?>