<?PHP

class RegisterController extends Controller {
	
	private
		$method,
		$name,
		$email,
		$p1,
		$p2,
		$ref;
	
	public function setMethod($method) {
		$this->method = $method;
	}
	
	public function setRef($ref) {
		$this->ref = $ref;
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
			exit(header("location: {$SITE['url']}register"));
		}
	}
	
	private function processGet() {
		global $SITE;
		
		if(empty($this->ref) || !UserReferralCode::isValidCode($this->ref)) {
			return array();
		}
		return array('ref' => $this->ref);
	}
	
	private function processPost() {
		global $SITE;
		
		# name, email, p1, and p2 are required
		# ref is optional, so you can ignore it entirely and not even have it in the form
		$_SESSION['flash'] =
			array(
				'status' => 'failure',
				'name' => $this->name,
				'email' => $this->email,
				'ref' => $this->ref,
			);
		
		$user = User::createUser($this->name, $this->email, $this->p1, $this->p2);
		if(!is_object($user) || get_class($user) !== 'User') {
			$_SESSION['flash']['msg'] = $user;
			exit(header("location: {$SITE['url']}register"));
		}
		
		if(isset($this->ref)) {
			$codeTest = UserReferralCode::isValidCode($this->ref);
			if($codeTest !== true) {
				$_SESSION['flash']['msg'] = "Invalid referral code. Please try another or leave it empty.";
				exit(header("location: {$SITE['url']}register"));
			}
			
			$uID = UserReferralCode::getUserIDByReferralCode($this->ref);
			// establish referral
			if(!ReferredUser::isReferral($uID, $user->module_id)) {
				$ruArray =
					array(
						'referral_id' => $uID,
						'user_id' => $user->module_id,
						'created' => time(),
					);
				$ru = ReferredUser::create($ruArray);
			}
			
			// give credit to referring user ($uID)
			
			
			// give credit to referred user ($user->module_id)
			
			
		}
		
		# create referral code for registering user
		$refArray =
			array(
				'user_id' => $user->module_id,
				'code' => UserReferralCode::getUniqueName('code', 10),
				'created' => time(),
			);
		$ref = UserReferralCode::create($refArray);
		if(!is_object($ref) || get_class($ref) !== 'UserReferralCode') {
			$_SESSION['flash']['msg'] = $ref;
			exit(header("location: {$SITE['url']}register"));
		}
		
		$request = Request::createRequest($user->module_id, 'register', '');
		if(!is_object($request) || get_class($request) !== 'Request') {
			$_SESSION['flash']['msg'] = $request;
			exit(header("location: {$SITE['url']}register"));
		}
		
		$params = array('code' => $request->code);
		$email = Email::createEmail($user->module_id, 'register', $params);
		if(!is_object($email) || get_class($email) !== 'Email') {
			$_SESSION['flash']['msg'] = $email;
			exit(header("location: {$SITE['url']}register"));
		}
		
		$result = $email->sendMail();
		if($result !== true) {
			$_SESSION['flash']['msg'] = $result;
			exit(header("location: {$SITE['url']}register"));
		}
		
		# Don't send the user an error if the admin email fails
		$params = array();
		$adminEmail = Email::createEmail($user->module_id, 'registerAdmin', $params);
		$adminEmail->sendMail();
		
		unset($_SESSION['flash']['name'], $_SESSION['flash']['email'], $_SESSION['flash']['ref']);
		$_SESSION['flash']['status'] = 'success';
		$_SESSION['flash']['msg'] = "You are now registered! Please verify your email address by clicking the confirmation link that we just emailed you.";
		exit(header("location: {$SITE['url']}"));
	}
}

?>