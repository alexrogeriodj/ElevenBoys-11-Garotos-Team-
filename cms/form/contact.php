<?php
$this->formIni('contact');

	$this->field('T', 'Nome', 'name', '', 'W=50|FOCUS|PLACEHOLDER=Nome');
	$this->field('T', 'Email', 'email', '', 'W=50|PLACEHOLDER=Email');
	$this->field('T', 'Assunto', 'subject', '', 'W=50|PLACEHOLDER=Assunto');
	$this->field('A', 'Mensagem', 'message', '', 'COLS=50|ROWS=11|CLASS=colfull|PLACEHOLDER=Mensagem');
	
	$this->button(array('VAL=Enviar|SBM'));

$this->formEnd();
?>

<script type="text/javascript">
fnAfterSubmit = function($form, xml, op) {
	fnClearForm($form);
};
</script>