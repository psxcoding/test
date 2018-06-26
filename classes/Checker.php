<?php
require_once ('DB/DB.php');
class Checker
{
	function __construct() {
		$mysql = db::getInstance();
		$this->db = $mysql->dbh;
	}

	/**
     * Get user data
     *
     * @param  string $name
     * @return array | boolean $userData
     */
	public function getUserData($name)
	{
		$stmt = $this->db->prepare("SELECT * FROM `users` WHERE `name`=:name ");
		$stmt->bindParam(':name', $name, PDO::PARAM_STR);
		$stmt->execute();
		$userData = $stmt->fetch(PDO::FETCH_ASSOC);
		return $userData;
	}

	/**
     * Checks If user can use module function
     *
     * @param  array $userData
     * @param  string $function
     * @return boolean $return
     */
	public function userCanUseModuleFunction($userData,$function)
	{
		$flag = "functions"; //checking functions
		$return = false;

		$functionData = $this->getFunctionData($function); // get function data

		//if function exists on DB proceed
		if (is_array($functionData)) {
			// if user has group 
			if (!empty($userData["group_id"])) {

				$byGroup = $this->groupCanUseFunction($userData["group_id"],$functionData["id"],$flag); // check if user group has permission to access function
				if ($byGroup) {
					$return = true;
				}else{
					$byDirectRight = $this->userHasDirectRight($userData["id"],$functionData["id"],$flag); // check if user has direct permission to access function
					$return = $byDirectRight;
				}
			}else{
				//if user don't have group check users direct permission
				$byDirectRight = $this->userHasDirectRight($userData["id"],$functionData["id"],$flag);
				if ($byDirectRight) {
					$return = $byDirectRight;
				}else{
					// if user dont have direct permission 

					// check if function has module
					$moduleData = $this->getModuleByID($functionData["module_id"]);

					// if function has module
					if (is_array($moduleData)) {
						// check if user has direct permission to functions module
						$byDirectRight = $this->userHasDirectRight($userData["id"],$moduleData["id"],"modules");
						if ($byDirectRight) {
							$return = true;
						}else{
							$return = false;
						}
					}else{
						$return = false;
					}
				}
			}
		}else{
			//if no data on DB return false
			$return = false;
		}

		return $return;
	}

	/**
     * Get module by module_id
     *
     * @param  int $moduleID
     * @return array | boolean $response
     */
	public function getModuleByID($moduleID)
	{
		$stmt = $this->db->prepare("SELECT * FROM `modules` WHERE `id`=? ");
		$stmt->execute(array($moduleID));
		$response = $stmt->fetch(PDO::FETCH_ASSOC);
		return $response;
	}

	/**
     * Get Function data
     *
     * @param  string $function
     * @return array | boolean  $response
     */
	public function getFunctionData($function)
	{
		$stmt = $this->db->prepare("SELECT * FROM `functions` WHERE `name`=:name ");
		$stmt->bindParam(':name', $function, PDO::PARAM_STR);
		$stmt->execute();
		$response = $stmt->fetch(PDO::FETCH_ASSOC);
		return $response;
	}

	/**
     * Get Module data
     *
     * @param  string $module
     * @return array | boolean  $response
     */
	public function getModuleData($module)
	{
		$stmt = $this->db->prepare("SELECT * FROM `modules` WHERE `name`=:name ");
		$stmt->bindParam(':name', $module, PDO::PARAM_STR);
		$stmt->execute();
		$response = $stmt->fetch(PDO::FETCH_ASSOC);
		return $response;
	}

	/**
     * Checks if goup has right to access function
     *
     * @param  int $groupID
     * @param  int $sourceID
     * @param  string $flag
     * @return boolean  $return
     */
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

	/**
     * Checks if user has right to access function
     *
     * @param  int $userID
     * @param  int $sourceID
     * @param  string $flag
     * @return boolean  $return
     */
	public function userHasDirectRight($userID,$sourceID,$flag)
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

	/**
     * Checks if group has right to access module
     *
     * @param  int $userID
     * @param  int $sourceID
     * @param  string $flag
     * @return boolean $return
     */
	public function groupCanUseModule($groupID,$sourceID,$flag)
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

	/**
     * Checks if user has right to access module
     *
     * @param  array $userData
     * @param  string $module
     * @return boolean $return
     */
	public function userCanUseModule($userData,$module)
	{
		$flag = "modules"; // checking modules
		$return = false;

		$moduleData = $this->getModuleData($module); // get Module data from DB

		// If Module exists in DB
		if (is_array($moduleData)) {
			// if User has group start checking group right first
			if (!empty($userData["group_id"])) {
				$byGroup = $this->groupCanUseModule($userData["group_id"],$moduleData["id"],$flag);
				if ($byGroup) {
					$return = true; // if users group has right to access module return true 
				}else{
					// if user group don't have permission to access module check if he has direct permission
					$byDirectRight = $this->userHasDirectRight($userData["id"],$moduleData["id"],$flag);
					$return = $byDirectRight;
				}
			}else{
				// if user don't have group check if he has direct right to access module
				$byDirectRight = $this->userHasDirectRight($userData["id"],$moduleData["id"],$flag);
				$return = $byDirectRight;
			}
		}else{
			// if module don't exist on DB return false
			$return = false;
		}
		return $return;
	}

}