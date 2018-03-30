<?php
/**
 * Script para recriar as URL's das categorias.
 */
require('../core/Loader.php');
$loader = new Loader('Page');

$database  = $loader->getObject('DataBase', 'database');
$url       = $loader->getObject('Url');

$sql = "select " .
		"	category_id, " .
		"	name " .
		"from " .
		"	category " .
		"order by " .
		"	name";
		
$dbq = $database->query($sql);

while($row = $database->getArray($dbq)) {
	$url_alias = $url->makeAlias($url->clearUrl($row['name']));
	$url->saveUrl('category.php?id=' . $row['category_id'], $url_alias, 'categoria/');
}
?>