<?php
require('../core/Loader.php');
$loader = new Loader('Session', 'library');
$session = new Session($loader);

session_unset();
session_destroy();
header('Location:login.php');
?>