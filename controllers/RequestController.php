<?PHP

class RequestController {
	
	public function __construct($code) {
		if(!empty($code)) {
			return $this->go($code);
		}
	}
	
	public function go($code) {
		global $SITE;
		
		if(!Request::isValidCode($code)) {
			exit(header("location: {$SITE['url']}"));
		}
		
		$request = new Request(Request::getRequestIDByCode($code));
		
		if($request->type === 'register') {
			$_SESSION['flash']['status'] = 'failure';
			
			$request->setRecord('status', 'complete');
			$request->setRecord('updated', time());
			$user = new User($request->user_id);
			$result = $user->setRecord('type', 'user');
			if($result !== true) {
				$_SESSION['flash']['msg'] = "Error: Failed to confirm email address.";
				exit(header("location: {$SITE['url']}"));
			}
			$user->setRecord('updated', time());
			
			$_SESSION['flash']['status'] = 'success';
			$_SESSION['flash']['msg'] = "Your email address has been confirmed. You may now sign in.";
			exit(header("location: {$SITE['url']}signin"));
		}
		else if($request->type === 'password') {
			
		}
	}
	
}

?>