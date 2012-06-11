<?PHP

class Contact extends Module {

	protected static
		$module_name = 'contact',
		$table_name = 'contacts',
		$allFields =
			array(
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'name',
					'type' => 'string',
					'max' => 100,
					'min' => 1,
				),
				array(
					'name' => 'email',
					'type' => 'string',
					'max' => 255,
					'min' => 1,
				),
				array(
					'name' => 'text',
					'type' => 'string',
					'max' => -1,
					'min' => 1,
				),
				array(
					'name' => 'subject',
					'type' => 'string',
					'max' => 255,
					'min' => 1,
				),
				array(
					'name' => 'user_id',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
			);
	
	public
		$created, $name, $email, $text, $subject, $user_id;

	
	public static function createContact($name, $email, $subject, $text, $uID=0) {
		global $SITE;
		
		if(empty($name) || empty($text)) {
			return "Error: Empty name or message.";
		}
		else if(strlen($subject) < 1 || strlen($subject) > 255) {
			return "Error: Subject must less than 100 characters. Go Back to keep the form filled out, and try again.";
		}
		else if(preg_match("/^[\w\.%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}(?:\.[A-Z]{2,4})?$/i", $email) == 0) {
			return "Error: Invalid email address. Go Back to keep the form filled out, and try again.";
		}
		
		$conArray =
			array(
				'name' => $name,
				'email' => $email,
				'subject' => $subject,
				'text' => $text,
				'user_id' => $uID,
				'created' => time(),
			);
		
		return Contact::create($conArray);
	}
	
}

?>