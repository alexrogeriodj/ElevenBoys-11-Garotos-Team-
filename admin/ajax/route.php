<?php
require('../../core/Loader.php');
$loader = new Loader();

$url = $loader->getObject('Url');

echo json_encode(array(
	'route' => $url->makeAlias($url->clearUrl($text), isset($query) ? $query : null)
));
?>