<?PHP

class ContactController extends Controller {
	
	private
		$user_id,
		$name,
		$email,
		$subject,
		$message;
	
	public function go() {
		global $SITE;
		
		# name, email, and message are required
		$_SESSION['flash'] =
			array(
				'status' => 'failure',
				'name' => $this->name,
				'email' => $this->email,
				'subject' => $this->subject,
				'message' => $this->message,
			);
		
		$contact = Contact::createContact($this->name, $this->email, $this->subject, $this->message, $this->user_id);
		if(!is_object($contact) || get_class($contact) !== 'Contact') {
			$_SESSION['flash']['msg'] = $contact;
			exit(header("location: {$SITE['url']}contact"));
		}
		
		$params = array('name' => $this->name, 'email' => $this->email, 'subject' => $this->subject, 'message' => $this->message);
		$email = Email::createEmail($user->module_id, 'contact', $params);
		if(!is_object($email) || get_class($email) !== 'Email') {
			$_SESSION['flash']['msg'] = $email;
			exit(header("location: {$SITE['url']}contact"));
		}
		
		$result = $email->sendMail();
		if($result !== true) {
			$_SESSION['flash']['msg'] = $result;
			exit(header("location: {$SITE['url']}contact"));
		}
		
		# Don't send the user an error if the admin email fails
		$params =
			array(
				'cID' => $contact->module_id,
				'name' => $this->name,
				'email' => $this->email,
				'subject' => $this->subject,
				'message' => $this->message,
				'created' => $contact->created,
			);
		$adminEmail = Email::createEmail($user->module_id, 'contactAdmin', $params);
		$adminEmail->sendMail();
		
		unset($_SESSION['flash']['name'], $_SESSION['flash']['email'], $_SESSION['flash']['message']);
		$_SESSION['flash']['status'] = 'success';
		$_SESSION['flash']['msg'] = "Your message has been received. We'll get back to you as soon as possible.";
		exit(header("location: {$SITE['url']}contact"));
	}
	
}

?>