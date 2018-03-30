<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin');

$database  = $page->get('database');
$functions = $page->get('functions');
$url       = $loader->getObject('Url');

$page->header('Categorias', 'form.css', null);

$selfLink = basename($_SERVER['PHP_SELF']);
$formName = str_replace('.php', '', $selfLink);

if(!isset($op)) {
	
	$page->tableIni();
	
		$page->lineIni('HEAD');
			$page->cell('Título', 'W=80%');
			$page->cell('Tipo', 'W=20%');
		$page->lineEnd();
		
		$sqlpag = "select count(category_id) from category";
		$pager = $page->pager($database->get($sqlpag), $selfLink);
		
		$sql = "select " .
				"	category_id, " .
				"	name, " .
				"	status " .
				"from " .
				"	category " .
				"order by " .
				"	name";
				
		$dbq = $database->query($sql, $pager);

		while($row = $database->getObject($dbq)) {
			$page->lineIni("LINK={$selfLink}?op=A&amp;id=$row->category_id");
				$page->cell($row->name);
				$page->cell($functions->getStatus($row->status));
			$page->lineEnd();
		}
		
	$page->tableEnd();
	
	$page->button(array('VAL=Novo|LINK=' . $selfLink . '?op=I'));
	
	echo $page->getPager();
	
}
else if($op == 'I' or $op=='A') {
	
	if($op=='A') {
		$sql = "select * from category where category_id = {$id}";
		$obj = $database->getObject($database->query($sql));
	}
	
	$page->widgetIni('Geral');
	
		$page->formIni($formName);
		
			if($op == 'A') $url->field('URL:', 'category.php?id=' . $id, 'categoria/');
			
			$page->field('T', 'Título', 'name', $obj->name, 'W=100|ML=200|FOCUS|CLASS=colfull');
			
			$page->field('S', 'Status', 'status', $obj->status, 'VAL=1:0|TXT=Ativo:Inativo|CLASS=colfull');
			
			$page->field('H', '', 'id', $obj->category_id);
		
			$page->clear();
		
			$page->button(array(
				'VAL=Voltar|LINK=' . $selfLink,
				'VAL=Salvar|SBM',
				'VAL=Excluir|DEL|RTN=' . $selfLink
			));
		$page->formEnd();
	
	$page->formEnd();
	
}

$page->footer();
?>