<?PHP

class User extends Module {
	
	protected static
		$module_name = 'user',
		$table_name = 'users',
		$allFields =
			array(
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'updated',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'name',
					'type' => 'string',
					'max' => 100,
					'min' => 0,
				),
				array(
					'name' => 'email',
					'type' => 'string',
					'max' => 100,
					'min' => 1,
				),
				array(
					'name' => 'password',
					'type' => 'string',
					'max' => 128,
					'min' => 128,
				),
				array(
					'name' => 'salt',
					'type' => 'string',
					'max' => 128,
					'min' => 128,
				),
				array(
					'name' => 'type',
					'type' => 'string',
					'max' => 20,
					'min' => 0,
				),
			),
		$limFields = array('typeArray'),
		$typeArray = array('administrator', 'curator', 'pending', 'user', 'deleted');
	
	public
		$created, $updated, $email, $password, $salt, $type, $name;
	
	public static function isAdmin() {
		if(empty($_SESSION['id'])) {
			return false;
		}
		$query = "SELECT module_id FROM users WHERE module_id='".$_SESSION['id']."' AND type='administrator'";
		return mysql_num_rows(mysql_query($query)) == 1;
	}

	public static function getUsers() {
		$query = "SELECT module_id FROM users";
		$select = mysql_query($query);
		while($temp = mysql_fetch_array($select)) {
			$array[] = $temp[0];
		}
		return $array;
	}

	public static function getNameByID($uID) {
		$uID = mysql_real_escape_string($uID);
		$query = "SELECT name FROM users WHERE module_id='$uID'";
		$array = mysql_fetch_array(mysql_query($query));
		return $array[0];
	}
	
	public static function getTypeByID($uID) {
		$uID = mysql_real_escape_string($uID);
		$query = "SELECT type FROM users WHERE module_id='$uID'";
		$array = mysql_fetch_array(mysql_query($query));
		return $array[0];
	}
	
	public static function emailExists($e) {
		$e = mb_strtolower(mysql_real_escape_string($e));
		$query = "SELECT module_id FROM users WHERE LOWER(email)='$e'";
		return mysql_num_rows(mysql_query($query)) == 1;
	}
	
	public static function nameExists($n) {
		$n = mb_strtolower(mysql_real_escape_string($n));
		$query = "SELECT module_id FROM users WHERE LOWER(name)='$n'";
		return mysql_num_rows(mysql_query($query)) == 1;
	}
	
	public static function getUserIDByEmail($e) {
		$e = mysql_real_escape_string(mb_strtolower($e));
		$query = "SELECT module_id FROM users WHERE LOWER(email)='$e'";
		$select = mysql_query($query);
		if(mysql_num_rows($select) > 0) {
			$array = mysql_fetch_array($select);
			return (int)$array[0];
		}
		else {
			return false;
		}
	}
	
	public static function getUserIDByName($n) {
		$n = mysql_real_escape_string(mb_strtolower($n));
		$query = "SELECT module_id FROM users WHERE LOWER(name)='$n'";
		$select = mysql_query($query);
		if(mysql_num_rows($select) > 0) {
			$array = mysql_fetch_array($select);
			return (int)$array[0];
		}
		else {
			return false;
		}
	}
	
	public static function createHashAndSaltFromInput($p) {
		$bcrypt = new Bcrypt(15);
		$cryptArray = $bcrypt->createHashAndSaltFromInput($p);
		$hash = $cryptArray['hash'];
		$salt = $cryptArray['salt'];
		return $bcrypt->verify($p, $salt, $hash) === true ? $cryptArray : false;
	}
	
	public static function getHashFromInputAndSalt($p, $salt) {
		$bcrypt = new Bcrypt(15);
		$hash = $bcrypt->getHashFromInputAndSalt($p, $salt);
		return $bcrypt->verifyInputAndSaltWithHash($p, $salt, $hash) === true ? $cryptArray : false;
	}
	
	public function deleteAccount() {
		$query = "UPDATE users SET type='deleted', email='', salt='', password='', updated='".time()."' WHERE module_id='{$this->module_id}'";
		return mysql_query($query);
	}
	
	public static function createUser($name='', $email='', $p1='', $p2='') {
		# Name must be valid and unused
		if(preg_match("/\W/", $name) > 0) {
			return "Error: Invalid user name. It may only contain letters, numbers and underscores.";
		}
		if(User::nameExists($name)) {
			return "Error: User name $name is already associated with an account.";
		}
		
		# Email must be valid and unused
		if(preg_match("/^[\w\.%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}(?:\.[A-Z]{2,4})?$/i", $email) == 0) {
			return "Error: Invalid email address.";
		}
		if(User::emailExists($email)) {
			return "Error: Email address $email is already associated with an account.";
		}
		
		# Passwords must match each other
		if(empty($p1) || empty($p2)) {
			return "Error: Passwords cannot be empty.";
		}
		if($p1 !== $p2) {
			return "Error: The passwords do not match.";
		}
		
		$cryptArray = User::createHashAndSaltFromInput($p1);
		if($cryptArray === false) {
			return "Error: Password encryption failed.";
		}
		
		# Create user account
		$userArray =
			array(
				'type' => 'pending',
				'name' => $name,
				'password' => $cryptArray['hash'],
				'email' => mb_strtolower($email),
				'salt' => $cryptArray['salt'],
				'updated' => time(),
				'created' => time(),
			);
		return User::create($userArray);
	}
	
	public static function searchUsers($s='', $no=array()) {
		$insert = '';
		for($i = 0; !empty($no[$i]); $i++) {
			$insert .= " AND name<>'".$no[$i]."'";
		}
		$query = "SELECT DISTINCT module_id FROM users WHERE name LIKE '%$s%' $insert";
		$select = mysql_query($query);
		$result = array();
		while($temp = mysql_fetch_array($select)) {
			$result[] = $temp[0];
		}
		return $result;
	}

}

?>