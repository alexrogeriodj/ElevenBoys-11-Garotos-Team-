<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'agenda');

$database  = $page->get('database');
$functions = $page->get('functions');
$session   = $page->get('session');

$page->header('Usuários', 'form.css', 'account.js');

if($session->hasAccess('USR')) {
	
	if(!isset($op)) {
		$page->widgetIni('Usuários', 'lista', "col-md-12 col-sm-12 col-xs-12");
		$page->tableIni();
		
			$page->lineIni('HEAD');
				$page->cell('Nome', 'W=30%');
				$page->cell('Username', 'W=13%');
				$page->cell('Email', 'W=35%');
				$page->cell('Tipo', 'W=12%');
				$page->cell('Status', 'W=10%');
			$page->lineEnd();
			
			$sqlpag = "select count(account_id) from account";
			$pager = $page->pager($database->get($sqlpag), 'account.php');
			
			$sql = "select " .
					"	a.account_id, " .
					"	a.username, " .
					"	a.name, " .
					"	a.email, " .
					"	a.status, " .
					"	b.name as type " .
					"from " .
					"	account a, " .
					"	account_type b " .
					"where " .
					"	a.account_type_id = b.account_type_id " .
					"order by " .
					"	a.name";
					
			$dbq = $database->query($sql, $pager);
			while($row = $database->getObject($dbq)) {
				$page->lineIni("LINK=account.php?op=A&amp;id=$row->account_id");
					$page->cell($row->name);
					$page->cell($row->username);
					$page->cell($row->email);
					$page->cell($row->type);
					$page->cell($functions->getStatus($row->status));
				$page->lineEnd();
			}
			
		$page->tableEnd();
		
		$page->button(array('VAL=Novo|LINK=account.php?op=I'));
		
		echo $page->getPager();
		$page->widgetEnd();
	}
	else if($op == 'I' or $op=='A') {
		
		if($op=='A') {
			$sql = "select * from account where account_id = {$id}";
			$obj = $database->getObject($database->query($sql));
		}
		$page->widgetIni('Usuário', $obj->name, "col-md-12 col-sm-12 col-xs-12");
		$page->formIni('account');
		
			$page->field('T', 'Nome completo', 'name', $obj->name, 'W=60|FOCUS|CLASS=col-md-12 col-sm-9 col-xs-12');
			$page->field('T', 'Email', 'email', $obj->email, 'W=60|CLASS=col-md-12 col-sm-9 col-xs-12');
			
			$page->field('T', 'Usuário', 'username', $obj->username, 'W=30|CLASS=col-md-6 col-sm-9 col-xs-12');
			
			
			$page->field('P', 'Senha', 'password', '', 'W=30|CLEAR|CLASS=col-md-6 col-sm-9 col-xs-12');
			$page->field('P', 'Conf. Senha', 'confPassword', '', 'W=30|CLASS=col-md-6 col-sm-9 col-xs-12');
			
			
			$page->field('T', 'Telefone', 'phone', $obj->phone, 'W=14|MASK=phone|CLASS=col-md-6 col-sm-9 col-xs-12');
			$page->field('T', 'Nascimento', 'birthday', $functions->formatDate2($obj->birthday), 'W=10|MASK=date|CLASS=col-md-6 col-sm-9 col-xs-12');
			
			$page->field('T', 'Endereço', 'address', $obj->address, 'W=80|ML=200|CLEAR|CLASS=col-md-12 col-sm-9 col-xs-12');
			
			$page->field('T', 'Cidade', 'city', $obj->city, 'W=50|CLASS=col-md-3 col-sm-9 col-xs-12');
			$page->field('S', 'Estado', 'state', $obj->state_id, 'LEFT|CLASS=col-md-3 col-sm-9 col-xs-12', 
				'select state_id, name from state order by name');
			
			$page->field('S', 'Tipo', 'type', $obj->account_type_id, 'CLASS=col-md-3 col-sm-9 col-xs-12', 
				'select account_type_id, name from account_type');
			$page->field('S', 'Status', 'status', $obj->status, 'VAL=1:0|TXT=Ativo:Inativo|CLASS=col-md-3 col-sm-9 col-xs-12');
			
			$page->field('H', '', 'id', $obj->account_id);
			echo '<div class="clear"></div>';
			$page->button(array(
				'VAL=Voltar|LINK=account.php',
				'VAL=Salvar|SBM',
				'VAL=Excluir|DEL|RTN=account.php'
			));
			
		$page->formEnd();
		$page->widgetEnd();
	}
	
}
else echo $session->getMessage();

$page->footer();
?>