<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'agenda');

$database  = $page->get('database');
$functions = $page->get('functions');

$page->header('Agenda', 'form.css', 'agenda.js');

$selfLink = basename($_SERVER['PHP_SELF']);
$formName = str_replace('.php', '', $selfLink);

if(!isset($op)) {
	
	$page->widgetIni('Agenda', 'Lista');
	
	$page->tableIni('');
	
	$page->lineIni('HEAD');
	$page->cell('Sala', 'W=30%');
	$page->cell('Evento', 'W=40%');
	$page->cell('Data', 'W=15%');
	$page->cell('Data Fim', 'W=15%');
	$page->cell('Status', 'W=15%');
	$page->lineEnd();
	
	$sql = "select " .
			"	id, " .
			"	room_id, " .
			"	title, " .
			"	status, " .
			"	date_fim, " .
			"	date_ini " .
			"from " .
			"	schedule a " .
			"order by " .
			"	date_added desc";
	
	$dbq = $database->query($sql, array(0, 5));
	while($row = $database->getObject($dbq)) {
		$page->lineIni("LINK=agenda.php?op=A&amp;id=$row->id");
		$page->cell($row->room_id);
		$page->cell($row->title);
		$page->cell($functions->formatDate($row->date_ini));
		$page->cell($functions->formatDate($row->date_fim));
		$page->cell($functions->getStatus($row->status));
		$page->lineEnd();
	}
	
	$page->tableEnd();
	
	$page->button(array('VAL=Agendar Sala|LINK=agenda.php?op=I|CLASS:field'));
	
	$page->widgetEnd();
	
	?>
	
	<?php 
}
else if($op == 'I' or $op=='A') {
	
	
		if($op == 'A') {
			$sql = "select * from schedule where id = {$id}";
			$obj = $database->getObject($database->query($sql));
		}
	
		$page->formIni('agenda');
		
		$page->widgetIni('Agenda', $obj->title, "col-md-12 col-sm-12 col-xs-12");
		
		$page->field('S', 'Sala', 'room_id', $obj->room_id, 'LEFT|CLASS=col-md-3 col-sm-9 col-xs-12',
				'select id, title from room order by title');
		
		$page->field('T', 'Título', 'title', $obj->title, 'W=60|ML=100|FOCUS|CLASS=col-md-9 col-sm-9 col-xs-12');
		$page->field('T', 'Data', 'data', $obj->data, 'MASK=99/99/9999|CLASS=col-md-4 col-sm-9 col-xs-12');
		$page->field('S', 'Turno', 'turno', $obj->turno, 'VAL=0:1:2:3|TXT=Selecione:Manhã:Tarde:Noite|CLASS=col-md-4 col-sm-9 col-xs-12');
		$page->field('S', 'Aulas', 'aulas', $obj->aulas, 'VAL=0:1:2|TXT=Selecione:Aula 1 - Aula 2:Aula 3 - Aula 4|CLASS=col-md-4 col-sm-9 col-xs-12');
		
		$page->field('A', 'Descrição do agendamento', 'content', $obj->content, 'COLS=100|ROWS=6|CLEAR|CLASS=col-md-12 col-sm-9 col-xs-12');
				
		echo '<div class="clear"></div>';
		
		$page->field('H', '', 'id', $obj->id);
		$dateLink = $functions->formatDate($obj->date_ini, 'Y-m-d');
		
		$page->button(array(
				"VAL=Ver Agendamento|LINK=../index.php?room=$obj->room_id&date=$dateLink",
				'VAL=Voltar|LINK=agenda.php',
				'VAL=Salvar|SBM',
				'VAL=Excluir|DEL|RTN=agenda.php'
		));
		$page->formEnd();
		?>
	
	<?php	
	$page->widgetEnd();
}




$page->footer();
?>