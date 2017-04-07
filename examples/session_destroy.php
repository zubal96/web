<?php
require 'config.php';
session_start();
$_SESSION = array();
session_destroy();
header('Location: http://web-2/examples/index.php');
?>