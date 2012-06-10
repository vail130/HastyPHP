<?PHP

class ForgotPasswordController {
	
	public function go($email) {
		global $SITE;
		
		$_SESSION['flash'] =
			array(
				'status' => 'failure',
				'email' => $email,
			);
		
		$uID = User::getUserIDByEmail($email);
		if($uID === false) {
			$_SESSION['flash']['msg'] = "Invalid email address $email. Please try again.";
			exit(header("location: {$SITE['url']}forgotpassword"));
		}
		
		$request = Request::createRequest($uID, 'password', '');
		if(!is_object($request) || get_class($request) !== 'Request') {
			$_SESSION['flash']['msg'] = $request;
			exit(header("location: {$SITE['url']}forgotpassword"));
		}
		
		$params = array('code' => $request->code);
		$email = Email::createEmail($uID, 'forgotPassword', $params);
		if(!is_object($email) || get_class($email) !== 'Email') {
			$_SESSION['flash']['msg'] = $email;
			exit(header("location: {$SITE['url']}forgotpassword"));
		}
		
		$result = $email->sendMail();
		if($result !== true) {
			$_SESSION['flash']['msg'] = $result;
			exit(header("location: {$SITE['url']}forgotpassword"));
		}
		
		unset($_SESSION['flash']['email']);
		$_SESSION['flash']['status'] = 'success';
		$_SESSION['flash']['msg'] = "We've just sent you a link to reset your password to the email address you specified.";
		exit(header("location: {$SITE['url']}forgotpassword"));
	}
}

?>