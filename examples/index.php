<!--https://vk.com/dev/authcode_flow_user-->
<?php
require 'config.php';
$flag1=false;
$flag2=false;
session_start();

	if ((isset($_GET['code']) || (isset($_SESSION['code'])))&&($_GET['state']=='group')) {
		if (isset($_GET['code']) & !(isset($_SESSION['code']))) {
			$_SESSION['code'] = $_GET['code'];       
		}
		header('Location: http://web-2/examples/main.php');
	}
	else{
		header('Location:' . config::$get_access_code_url . http_build_query(config::$params_code));
	}

	if ((isset($_GET['code']) || (isset($_SESSION['code_user'])))&&($_GET['state']=='user')) {
		if (isset($_GET['code']) & !(isset($_SESSION['code_user']))) {
			$_SESSION['code_user'] = $_GET['code'];        
		}
		header('Location: http://web-2/examples/main.php');
	}
	else{
		header('Location:' . config::$get_access_code_url . http_build_query(config::$params_code_user));
	}
?>
