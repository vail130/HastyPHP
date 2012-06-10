<?PHP

class ResetPasswordController {
	
	private
		$method,
		$code,
		$op,
		$p1,
		$p2;
	
	public function setParams($params) {
		if(empty($params)) {
			return;
		}
		
		foreach($params as $key => $value) {
			if(isset($this->$key)) {
				$this->$key = $value;
			}
		}
	}
	
	public function setMethod($method) {
		$this->method = $method;
	}
	
	public function setCode($code) {
		$this->code = $code;
	}
	
	public function go() {
		global $SITE;
		
		if($this->method === 'POST') {
			return $this->processPost();
		}
		else if($this->method === 'GET') {
			return $this->processGet();
		}
		else {
			exit(header("location: {$SITE['url']}"));
		}
	}
	
	private function processGet() {
		global $SITE;
		
		if(empty($this->code)) {
			exit(header("location: {$SITE['url']}"));
		}
		
		$rID = Request::getRequestIDByCode($this-code);
		if($rID === false) {
			exit(header("location: {$SITE['url']}"));
		}
		
		$request = new Request($rID);
		if($request->type !== 'password') {
			exit(header("location: {$SITE['url']}"));
		}
		
		return array('code', $this->code);
	}
	
	private function processPost() {
		global $SITE;
		
		$_SESSION['flash']['status'] = 'failure';
		
		$SESSION = SessionController::validSession();
		
		// This statement needs to stay here
		// Signed-out visitors will go back to the main page
		$endpoint = "{$SITE['url']}";
		
		// If there is a session, the user must reset password from settings.php,
		// and the request code will just be created right here
		if($SESSION === true) {
			// Signed-in users will go back to settings page
			$endpoint = "{$SITE['url']}settings";
			
			// If old password isn't set, go to settings page
			if(!isset($this->op)) {
				exit(header("location: $endpoint"));
			}
		
			$uID = SessionController::getUserID();
			$u = new User($uID);
			
			// Authenticate old password with user's salt
			$testHash = User::getHashFromInputAndSalt($this->op, $u->salt);
			if($testHash !== $u->password) {
				$_SESSION['flash']['msg'] = "Error: Old password is incorrect. Please try again.";
				exit(header("location: $endpoint"));
			}
			
			// Create request code for this password change
			$req = Request::createRequest($uID, 'password', '');
			if(!is_object($req) || get_class($req) !== 'Request') {
				$_SESSION['flash']['msg'] = $req;
				exit(header("location: $endpoint"));
			}
			
			// Set request code for controller
			$this->setCode($req->code);
		}
		
		// If the code is invalid, go to main page
		if(!Request::isValidCode($this->code)) {
			exit(header("location: $endpoint"));
		}
			
		$request = new Request(Request::getRequestIDByCode($this->code));
		
		// If this request code wasn't for a password change or the passwords
		// are not set, go to main page
		if($request->type !== 'password' || !isset($this->p1) || !isset($this->p2)) {
			exit(header("location: $endpoint"));
		}
		
		// No active session
		if($SESSION !== true) {
			// This statement needs to stay here
			// Signed-out visitors will go back to the reset password page
			$endpoint = "{$SITE['url']}resetpassword/{$this->code}";
		}
		
		// If passwords don't match, 
		if($this->p1 !== $this->p2) {
			$_SESSION['flash']['msg'] = "Error: Mismatching passwords. Please try again.";
			exit(header("location: $endpoint"));
		}
		
		$user = new User($request->user_id);
		
		// Get new hash and salt from password
		$cryptArray = User::createHashAndSaltFromInput($this->p1);
		if($cryptArray === false) {
			$_SESSION['flash']['msg'] = "Error: Password encryption failed. The password has not been updated.";
			exit(header("location: $endpoint"));
		}
		
		// Set new hash and salt for the user
		$result = $user->setModule(array('password' => $cryptArray['hash'], 'salt' => $cryptArray['salt']));
		if($result !== true) {
			$_SESSION['flash']['msg'] = "Error: Failed to update password. Please try again.";
			exit(header("location: $endpoint"));
		}
		
		// Update user's update time
		$user->setRecord('updated', time());
		
		// Update status of the request
		$request->setModule(array('status' => 'complete', 'updated' => time()));
		
		// No active session
		if($SESSION !== true) {
			// This statement needs to stay here
			// Signed-out visitors will go to signin page
			$endpoint = "{$SITE['url']}signin";
		}
		
		// Create email model for this action
		$params = array();
		$email = Email::createEmail($user->module_id, 'changePassword', $params);
		if(!is_object($email) || get_class($email) !== 'Email') {
			$_SESSION['flash']['msg'] = $email;
			exit(header("location: $endpoint"));
		}
		
		// Send email module just created
		$result = $email->sendMail();
		if($result !== true) {
			$_SESSION['flash']['msg'] = $result;
			exit(header("location: $endpoint"));
		}
		
		$_SESSION['flash']['status'] = 'success';
		$_SESSION['flash']['msg'] = "Your password has been updated.";
		// No active session
		if($SESSION !== true) {
			$_SESSION['flash']['msg'] .= " You may now sign in.";
		}
		exit(header("location: $endpoint"));
	}
}

?>