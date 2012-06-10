<?PHP

class Email extends Module {

	protected static
		$module_name = 'email',
		$table_name = 'emails',
		$allFields =
			array(
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
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
					'min' => 0,
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
				array(
					'name' => 'status',
					'type' => 'string',
					'max' => 10,
					'min' => 0,
				),
			),
		$limFields = array('statusArray'),
		$statusArray = array('pending', 'sent');
	
	public
		$created, $email, $text, $subject, $status, $user_id;
	
	private function getFooter() {
		return
			"<p>This email was produced automatically by
			<a target='_blank' style='color:black;' href='{$SITE['url']}'>{$SITE['url']}</a> because
			of an action that was taken connected to the account registered to this email address.
			If you are receiving this email in error or you want to remove your
			information from our database, you can go to 
			<a target='_blank' style='color:black;' href='{$SITE['url']}'>{$SITE['url']}</a>,
			sign in to deactivate this account or contact us at
			<a target='_blank' style='color:black;' href='{$SITE['url']}contact'>{$SITE['url']}contact</a>.</p>";
	}
									
	public static function createEmail($uID, $type, $params=array()) {
		global $SITE, $MAIL;
		
		if($uID > 0) {
			$user = new User($uID);
		}
		
		if($type == 'register') {
			if(empty($params['code'])) {
				return "Error: No email confirmation code.";
			}
			
			$recipient = $user->email;
			$subject = 'Confirmation of Registration';
			
			$content =
				"<p>Dear {$user->name},</p>
				<p>We just received your registration information. All you need to do now is click
					on the link below to confirm this email address.</p>
				<p><a target='_blank' style='background-color:white;color:black;font-weight:bold;' href='{$SITE['url']}request/{$params['code']}'>{$SITE['url']}request/{$params['code']}</a></p>";
		}
		else if($type == 'registerAdmin') {
			$recipient = $MAIL['contact'];
			$subject = 'Notification of Registration';
			$content =
				"<p>Dear Admin,</p>
				<p>A new registration has been submitted to the database:</p>
				<ul>
					<li>User ID: $uID</li>
					<li>User Name: {$user->name}</li>
					<li>User Type: {$user->type}</li>
					<li>Email Address: {$user->email}</li>
					<li>Time: ".date("g:ia, m/j/Y", $user->created)."</li>
				</ul>";
		}
		else if($type == 'newContact') {
			$recipient = $params['email'];
			$subject = 'Confirmation of Feedback';
			
			$content =
				"<p>Dear <span style='background-color:white;color:black;'>{$params['name']}</span>,</p>
				<p>We just received your feedback, and we'll read it as soon as we can. We really
					appreciate you taking the time to contact us. We're always trying to serve our
					uesrs better, so if you think of anything else you want to let us know, please
					feel free.</p>
				<h3>You said:</h3>
				<p>{$params['text']}</p>";
		}
		else if($type == 'contactAdmin') {
			$recipient = $MAIL['contact'];
			$subject = 'Notification of Feedback';
			
			$content =
				"<p>Dear Admin,</p>
				<p>A new contact has been submitted to the database:</p>
				<ul>
					<li>Contact ID: {$params['cID']}</li>
					<li>Time: ".date("g:ia, m/j/Y", $params['datetime'])."</li>
					<li>Name: {$params['name']}</li>
					<li>Email Address: {$params['email']}</li>
					<li>User ID: ".($uID == 0 ? 'No user account' : $uID)."</li>
					<li>Message:<br/>{$params['text']}</li>
				</ul>";
		}
		else if($type == 'forgotPassword') {
			if(empty($params['code'])) {
				return "Error: No email confirmation code.";
			}
			
			$recipient = $user->email;
			$subject = "Reset your password";
			
			$content =
				"<p>Dear <span style='background-color:white;color:black;'>{$user->name}</span>,</p>
				<p>The \"Forgot Password\" form was filled out at <a style='background-color:white;color:black;' href='{$SITE['url']}forgotpassword'>{$SITE['url']}forgotpassword</a>
					with this email address. Follow this link to reset your password:</p>
				<p><a style='background-color:white;color:black;' href='{$SITE['url']}resetpassword/{$params['code']}'>{$SITE['url']}/resetpassword/{$params['code']}</a></p>";
		}
		else if($type == 'changePassword') {
			$recipient = $user->email;
			$subject = "Password changed";
			
			$content =
				"<p>Dear <span style='background-color:white;color:black;'>{$user->name}</span>,</p>
				<p>Your password for <a style='background-color:white;color:black;' href='{$SITE['url']}'>{$SITE['url']}</a>
					has been changed. If this was a mistake or someone else did this, you can use the \"Forgot Password\"
					form  at
					<a style='background-color:white;color:black;' href='{$SITE['url']}forgotpassword'>{$SITE['url']}forgotpassword</a>
					to change it again.</p>";
		}
		else if($type == 'changeEmail') {
			if(empty($params['code'])) {
				return "Error: No email confirmation code.";
			}
			
			$recipient = $params['email'];
			$subject = "Email address change confirmation";
			
			$content =
				"<p>Dear <span style='background-color:white;color:black;'>{$user->name}</span>,</p>
				<p>Your email address for <a style='background-color:white;color:black;' href='{$SITE['url']}'>{$SITE['url']}</a>
					is being updated. If this was a mistake or someone else did this, you can ignore this email,
					and nothing will happen. If you want this email address to be your new, official one for the site,
					follow the link below:</p>
				<p><a style='background-color:white;color:black;' href='{$SITE['url']}request/{$params['code']}'>{$SITE['url']}/request/{$params['code']}</a></p>";
		}
		
		$eArray =
			array(
				'email' => $recipient,
				'type' => $type,
				'subject' => $subject,
				'status' => 'pending',
				'text' => $content,
				'user_id' => $uID,
				'created' => time(),
			);
		return Email::create($eArray);
	}
	
	public function sendMail() {
		global $MAIL, $SITE;
		
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPSecure = $MAIL['secure'];
		$mail->SMTPAuth   = true;
		$mail->Host       = $MAIL['host'];
		$mail->Port       = $MAIL['port'];
		$mail->Username   = $MAIL['user'];
		$mail->Password   = $MAIL['pass'];
		$mail->From       = $MAIL['from'];
		$mail->FromName   = $MAIL['fromName'];
		$mail->Subject    = $this->subject;
		$mail->Body       = $this->getHTMLEmail();
		$mail->AltBody    = $this->getPlaintextEmail();
		$mail->WordWrap   = 50;
		$mail->AddAddress($this->email);
		$mail->IsHTML(true);
		$result = $mail->Send();
		if($result !== true) {
			return $result;
		}
		else {
			return $this->setRecord('status', 'sent');
		}
	}
	
	public static function getUnsentEmails() {
		$query = "SELECT module_id FROM emails WHERE status<>'sent'";
		$select = mysql_query($query);
		while($temp = mysql_fetch_array($select)) {
			$array[] = $temp[0];
		}
		return $array;
	}
	
	public function getHTMLEmail() {
		global $SITE, $MAIL;
		return
			"<table border='0' cellpadding='20' cellspacing='0' width='100%' height='100%' style='background-color:#eee;height: 100% !important; width: 100% !important;'>
			<tbody>
			<tr>
			<td align='center' valign='top'>
				<table border='0' cellpadding='0' cellspacing='0' width='550' style='background-color:white;'>
				<tbody>
				<tr>
					<td colspan='2' align='left' valign='top' width='100%'>
						<table border='0' cellpadding='0' cellspacing='10' width='100%'>
						<tbody>
							<tr>
								<td style='color:#222;font-family:Arial, sans;'>{$this->text}</td>
							</tr>
						</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan='2' align='left' valign='top' width='100%'>
						<table border='0' cellpadding='0' cellspacing='10' width='100%' style='margin-top:10px;'>
						<tbody>
						<tr>
							<td style='color:#aaa;font-family:Arial, sans;'>
								{$this->getFooter()}
							</td>
						</tr>
						</tbody>
						</table>
					</td>
				</tr>
				</tbody>
				</table>
			</td>
			</tr>
			</tbody>
			</table>";
	}
	
	public function getPlaintextEmail() {
		global $SITE;
		$find = array('</h1>', '</h2>', '</h3>', '</h4>', '</h5>', '</p>', '</td>', '</span>');
		return
			strip_tags(str_ireplace($find, "\n", stripslashes(stripslashes($this->text))))."\n\n
			________________________________________________\n
			".strip_tags($this->getFooter());
	}
	
}

?>