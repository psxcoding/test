<?php
require_once ('DB/DB.php');
class User
{
	function __construct() {
		$mysql = db::getInstance();
		$this->db = $mysql->dbh;
	}

	public function getUserData($name)
	{
		$stmt = $this->db->prepare("SELECT * FROM `users` WHERE `name`=:name ");
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		$userData = $stmt->fetch(PDO::FETCH_ASSOC);

		return $userData;
	}

	public function userCanUseModuleFunction($userData,$function)
	{
		$flag = "functions";
		$return = false;

		$functionData = $this->getFunctionData($function);
		if (is_array($functionData)) {
			if (!empty($userData["group_id"])) {

				$byGroup = $this->groupCanUseFunction($userData["group_id"],$functionData["id"],$flag);
				if ($byGroup) {
					$return = true;
				}else{
					$byDirectRight = $this->userHasDirectRight($userData["id"],$functionData["id"],$flag);
					$return = $byDirectRight;
				}

			}else{
				$byDirectRight = $this->userHasDirectRight($userData["id"],$functionData["id"],$flag);
				$return = $byDirectRight;
			}
		}else{
			$return = false;
		}

		return $return;
	}

	public function getFunctionData($function)
	{
		$stmt = $this->db->prepare("SELECT * FROM `functions` WHERE `name`=:name ");
		$stmt->bindParam(':name', $function, PDO::PARAM_STR);
		$stmt->execute();
		$response = $stmt->fetch(PDO::FETCH_ASSOC);

		return $response;
	}

	public function getModuleData($module)
	{
		$stmt = $this->db->prepare("SELECT * FROM `modules` WHERE `name`=:name ");
		$stmt->bindParam(':name', $module, PDO::PARAM_STR);
		$stmt->execute();
		$response = $stmt->fetch(PDO::FETCH_ASSOC);

		return $response;
	}

	public function groupCanUseFunction($groupID,$sourceID,$flag = "functions")
	{
		$return = false;

		$stmt = $this->db->prepare("SELECT * FROM `group_rights` WHERE `group_id`=? AND `source_id`=? AND `flag`=? ");
		$stmt->execute(array($groupID,$sourceID,$flag));
		$response = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($response) && !empty($response)) {
			$return = true;
		}else{
			$return = false;
		}

		return $return;
	}

	public function userHasDirectRight($userID,$sourceID,$flag = "functions")
	{
		$return = false;

		$stmt = $this->db->prepare("SELECT * FROM `user_rights` WHERE `user_id`=? AND `source_id`=? AND `flag`=? ");
		$stmt->execute(array($userID,$sourceID,$flag));
		$response = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($response) && !empty($response)) {
			$return = true;
		}else{
			$return = false;
		}

		return $return;
	}

	public function groupCanUseModule($groupID,$sourceID,$flag = "functions")
	{
		$return = false;

		$stmt = $this->db->prepare("SELECT * FROM `group_rights` WHERE `group_id`=? AND `source_id`=? AND `flag`=? ");
		$stmt->execute(array($groupID,$sourceID,$flag));
		$response = $stmt->fetch(PDO::FETCH_ASSOC);

		if (isset($response) && !empty($response)) {
			$return = true;
		}else{
			$return = false;
		}

		return $return;
	}

	public function userCanUseModule($userData,$module)
	{
		$flag = "modules";
		$return = false;

		$moduleData = $this->getModuleData($module);

		if (is_array($moduleData)) {
			if (!empty($userData["group_id"])) {

				$byGroup = $this->groupCanUseModule($userData["group_id"],$moduleData["id"],$flag);

				if ($byGroup) {
					$return = true;
				}else{
					$byDirectRight = $this->userHasDirectRight($userData["id"],$moduleData["id"],$flag);
					$return = $byDirectRight;
				}

			}else{
				$byDirectRight = $this->userHasDirectRight($userData["id"],$moduleData["id"],$flag);
				$return = $byDirectRight;
			}
		}else{
			$return = false;
		}

		return $return;
	}

}