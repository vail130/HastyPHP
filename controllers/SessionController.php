<?PHP

class SessionController {

	public function __construct() {
		
	}
	
	public function createSession($n, $p) {
		global $SITE;
		
		if(empty($n)) {
			$_SESSION['flash']['msg'] = "Error: No user name.";
			exit(header("location: {$SITE['url']}signin"));
		}
		if(empty($p)) {
			$_SESSION['flash']['msg'] = "Error: No password.";
			exit(header("location: {$SITE['url']}signin"));
		}
		
		$_SESSION['flash'] =
			array(
				'status' => 'failure',
				'name' => $n,
			);
		
		$uID = User::getUserIDByName($n);
		if($uID === false) {
			$_SESSION['flash']['msg'] = "Error: Invalid user name.";
			exit(header("location: {$SITE['url']}signin"));
		}
		
		$user = new User($uID);
		if($user->type === 'pending') {
			$_SESSION['flash']['msg'] = "Error: That account is not yet validated.";
			exit(header("location: {$SITE['url']}signin"));
		}
		
		$hash = User::getHashFromInputAndSalt($p, $user->salt);
		if($user->password !== $hash) {
			$_SESSION['flash']['msg'] = "Error: Invalid password.";
			exit(header("location: {$SITE['url']}signin"));
		}
		
		$_SESSION['id'] = $user->module_id;
		$_SESSION['name'] = $user->name;
		$_SESSION['type'] = $user->type;
		
		unset($_SESSION['flash']['name']);
		$_SESSION['flash']['status'] = 'success';
		exit(header(
			"location: ".(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $SITE['url'])
		));
	}
	
	public function destroySession() {
		global $SITE;
		
		session_unset(); 
		session_destroy();
		exit(header("location: {$SITE['url']}"));
	}

	public static function validSession() {
		//check to make sure the session variable is registered 
		return !empty($_SESSION['id']) && User::isValidID($_SESSION['id']);
	}
	
	public static function getSessionType() {
		//check to make sure the session variable is registered 
		return isset($_SESSION['type']) ? $_SESSION['type'] : false;
	}
	
}

?>