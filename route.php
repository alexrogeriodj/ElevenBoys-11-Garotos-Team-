<?php
$time = microtime(true);

require('core/Loader.php');
$loader = new Loader();

$routeObj = $loader->getObject('Route');

$_route = $routeObj->getRoute($route);

foreach($_GET as $k => $v) ${$k} = $v;

require($_route);

//echo "<!--Tempo de execução" . (microtime(true) - $time) . "s-->";
?>