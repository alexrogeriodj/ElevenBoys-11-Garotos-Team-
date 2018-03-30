<?php
require('../core/Loader.php');
$loader = new Loader('Page');

$page = new Page($loader, 'agenda');

$database  = $page->get('database');
$functions = $page->get('functions');

$page->header(
	'Administração', 
	'dashboard.css', 
	null, 
	array('pageTitle' => 'Dashboard')
);

?>
<a href='../index.php?room=1' class='btn btn-primary col-lg-12'>Ver Agenda</a>
<?php 

$page->footer();
?>