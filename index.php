<?php

require_once ('DB/DB.php');
require_once ('classes/Checker.php');

$mysql = db::getInstance();
$db = $mysql->dbh;

if (isset($_GET["username"])) {
	$userName = $_GET["username"];
	$chekerObj = new Checker();
	$userData = $chekerObj->getUserData($userName);
}

if (isset($_GET["module"])) {
	$module = $_GET["module"];
}
if (isset($_GET["function"])) {
	$function = $_GET["function"];
}

if (isset($module) && !empty($module)) {
	$userCanUseModule = $chekerObj->userCanUseModule($userData,$module);
	var_dump($userCanUseModule);
}elseif (isset($function) && !empty($function)) {
	$userCanUseModuleFunction = $chekerObj->userCanUseModuleFunction($userData,$function);
	var_dump($userCanUseModuleFunction);
}

if (empty($_GET)) {

	echo "<a href='/?username=vartotojas1&module=modulis1'>Ar vartotojas 1 gali naudotis moduliu 1</a>";
	echo "<br>";
	echo "<a href='/?username=vartotojas1&function=funkcija1'>Ar vartotojas 1 gali naudotis funkcija 1</a>";
	echo "<br>";
	echo "<a href='/?username=vartotojas4&function=funkcija2'>Ar vartotojas 4 gali naudotis funkcija 2</a>";
}


?>