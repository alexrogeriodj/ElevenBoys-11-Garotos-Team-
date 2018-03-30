<?php
header("Status: 404 Not Found");

if(!isset($loader)) {
	require('core/Loader.php');
	$loader = new Loader();
}

$loader->load('Page');
$page = new Page($loader);

$col1 = '<h2>Erro 404!</h2><p>Oops, ocorreu um erro. A página solicitada não foi encontrada!</p>';

$page->header(
	'Página não encontrada', 
	array('page.css'), 
	null, 
	array(
		'pageTitle' => 'Página não encontrada',
		'pageClass' => 'error-404'
	)
);
?>

<div class="not-found">
	<h2>404</h2>
	<h3>Oops, ocorreu um erro. a página solicitada não foi encontrada!</h3>
</div>

<?php $page->footer(); ?>