<?php
/**
 * Tipos de Base disponíves [TYPE]
 * MySQL         = MySQL
 * PostgreSQL    = PgSQL
 * Microsoft SQL = MsSQL
 * OBS: TYPE é case sensitive
 */

$cfg = array();

$cfg['DB_DEFAULT'] = array(
	'TYPE' => 'MySQL',
	'HOST' => 'localhost', 
	'USER' => 'root', 
	'PASS' => '', 
	'NAME' => 'agenda' 
);
?>