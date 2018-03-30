<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'admin');

$database = $page->get('database');

$page->header('Configuração: Email', 'form.css', 'config-mail.js');

$mailer = array(
	'smtp'     => 'SMTP',
	'sendmail' => 'Send Mail', 
	'qmail'    => 'QMail',
	'mail'     => 'Mail'
);

if(!isset($op)) {
	$_GET['op'] = $op = 'A';
	$id = "(select config_mail_id from config_mail where name = 'default')";
}

if(empty($op) or $op == 'L') {
	
	$page->tableIni();
	
		$page->lineIni('HEAD');
			$page->cell('Mailer', 'W=10%');
			$page->cell('Autenticação', 'W=10%');
			$page->cell('Charset', 'W=10%');
			$page->cell('Porta', 'W=10%');
			$page->cell('Host', 'W=50%');
			$page->cell('Key', 'W=10%');
		$page->lineEnd();
		
		$sqlpag = "select count(config_id) from config_mail";
		$pager = $page->pager($database->get($sqlpag), 'config-mail.php');
		
		$sql = "select * from config_mail";
				
		$dbq = $database->query($sql, $pager);
		while($row = $database->getObject($dbq)) {
			$page->lineIni("LINK=config-mail.php?op=A&amp;id=$row->config_mail_id");
				$page->cell($mailer[$row->mailer]);
				$page->cell($row->authenticate == 'S' ? 'Sim' : 'Não');
				$page->cell($row->charset);
				$page->cell($row->port);
				$page->cell($row->host);
				$page->cell($row->name);
			$page->lineEnd();
		}
		
	$page->tableEnd();
	
	echo $page->getPager();
	
}
else if($op == 'I' or $op=='A') {
	
	if($op=='A') {
		$sql = "select * from config_mail where config_mail_id = $id";
		$obj = $database->getObject($database->query($sql));
	}
	
	$page->formIni('config-mail');
	
		$page->field('T', 'Nome', 'name', $obj->name, 'W=32|ML=32|RO|CLASS=colset3');
		$page->field('S', 'Mailer', 'mailer', $obj->mailer, 
			'VAL=' . join(':', array_keys($mailer)) . '|TXT=' . join(':', $mailer) . '|CLASS=colset3 col2');
		$page->field('S', 'Charset', 'charset', $obj->charset, 'VAL=UTF-8:ISO-8859-1|CLASS=colset3 col3');
		
		$page->field('T', 'Porta', 'port', $obj->port, 'W=6|ML=4|MASK=9999|CLASS=colset3');
		$page->field('S', 'Segurança', 'security', $obj->security, 'VAL=:tls:ssl|TXT=Nenhuma:TLS:SSL|CLASS=colset3 col2');
		$page->field('S', 'Autenticação', 'authenticate', $obj->authenticate, 'VAL=S:N|TXT=Sim:Não|CLASS=colset3 col3');
		
		$page->field('T', 'Host', 'host', $obj->host, 'W=80|CLASS=fw100');
		$page->field('T', 'Usuário', 'username', $obj->username, 'W=80|CLASS=colset2');
		$page->field('T', 'Senha', 'password', $obj->password, 'W=20|CLASS=colset2 col2');
		$page->field('T', 'Email origem (de)', 'fromEmail', $obj->from_email, 'W=80|CLASS=colset2');
		$page->field('T', 'Nome origem (de)', 'fromName', $obj->from_name, 'W=80|CLASS=colset2 col2');
		
		$page->field('H', '', 'id', $obj->config_mail_id);
	
		echo '<div class="clear"></div>';
	
		$page->button(array(
			//'VAL=Voltar|LINK=config-mail.php',
			'VAL=Salvar|SBM'
		));
		
	$page->formEnd();
	
}

$page->footer();
?>