<?php

require_once ('DB/DB.php');
require_once ('classes/User.php');

$mysql = db::getInstance();
$db = $mysql->dbh;

	$userName = $_GET["username"];

	if (isset($_GET["module"])) {
		$module = $_GET["module"];
	}
	if (isset($_GET["function"])) {
		$function = $_GET["function"];
	}
	
	$userObj = new User();
	$userData = $userObj->getUserData($userName);


	if (isset($module) && !empty($module)) {
		$userCanUseModule = $userObj->userCanUseModule($userData,$module);
		var_dump($userCanUseModule);
	}elseif (isset($function) && !empty($function)) {
		$qq = $userObj->userCanUseModuleFunction($userData,$function);
		var_dump($qq);
	}


?>