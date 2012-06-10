<?PHP

class ResetPasswordController {
	
	private
		$method,
		$code,
		$p1,
		$p2;
	
	public function setParams($code, $p1, $p2) {
		$this->code = $code;
		$this->p1 = $p1;
		$this->p2 = $p2;
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
		
		if(!Request::isValidCode($this->code)) {
			exit(header("location: {$SITE['url']}"));
		}
			
		$request = new Request(Request::getRequestIDByCode($this->code));
		
		if($request->type !== 'password' || !isset($this->p1) || !isset($this->p2)) {
			exit(header("location: {$SITE['url']}"));
		}
		
		$_SESSION['flash']['status'] = 'failure';
		
		if($this->p1 !== $this->p2) {
			$_SESSION['flash']['msg'] = "Error: Mismatching passwords. Please try again.";
			exit(header("location: {$SITE['url']}resetpassword/{$this->code}"));
		}
		
		$request->setModule(array('status' => 'complete', 'updated' => time()));
		
		$user = new User($request->user_id);
		
		$cryptArray = User::createHashAndSaltFromInput($this->p1);
		if($cryptArray === false) {
			$_SESSION['flash']['msg'] = "Error: Password encryption failed. The password has not been updated.";
			exit(header("location: {$SITE['url']}resetpassword/{$this->code}"));
		}
		
		$result = $user->setModule(array('password' => $cryptArray['hash'], 'salt' => $cryptArray['salt']));
		if($result !== true) {
			$_SESSION['flash']['msg'] = "Error: Failed to update password. Please try again.";
			exit(header("location: {$SITE['url']}resetpassword/{$this->code}"));
		}
		$user->setRecord('updated', time());
		
		$_SESSION['flash']['status'] = 'success';
		$_SESSION['flash']['msg'] = "Your password has been updated. You may now sign in.";
		exit(header("location: {$SITE['url']}signin"));
	}
}

?>