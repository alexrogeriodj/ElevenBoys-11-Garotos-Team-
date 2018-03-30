<?php
$cfg = array();

$cfg['VERSION'] = '2';

$cfg['SESSION_NAME'] = 'induca';

$cfg['CRYPT_KEY'] = '=0a1b2c3d4e5f6g7h8i=';

$cfg['PAGER_FORMAT']    = '' .
		'<div class="page-nav first">' .
			'<a href="{first}" title="Primeira">Primeira</a>' .
		'</div>' .
		'<div class="page-nav prev">' .
			'<a href="{prev}" title="Página anterior">&lt; Anterior</a>' .
		'</div>' .
		'<div class="page-numbers">' .
			'... {numbers} ...' .
		'</div>' .
		'<div class="page-nav next">' .
			'<a href="{next}" title="Próxima página">Próxima &gt;</a>' .
		'</div>' .
		'<div class="page-nav last">' .
			'<a href="{last}" title="Última">Última</a>' .
		'</div>';

$cfg['DIR_THEME']    = 'theme';
?>