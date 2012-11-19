<?PHP

class Email extends Model {

  public
    $class = 'Email',
    $table = 'emails',
    $fields = array(
      'id' => array('type' => 'int'),
      'user_id' => array('type' => 'int', 'min' => 0),
      'email' => array('type' => 'string', 'max' => 255, 'min' => 1),
      'subject' => array('type' => 'string', 'max' => 255, 'min' => 1),
      'html' => array('type' => 'string'),
      'text' => array('type' => 'string'),
      'type' => array('type' => 'string', 'max' => 20, 'min' => 1, 'options' => array('register', 'approval', 'forgotpassword')),
      'status' => array('type' => 'string', 'max' => 10, 'min' => 1, 'options' => array('pending', 'sent')),
      'updated' => array('type' => 'int'),
      'created' => array('type' => 'int'),
    );

  public function createRecord($user, $type) {
    global $SITE, $MAIL;

    if(get_class($user) !== 'User' || empty($user->attributes['email'])) {
      return "Invalid user.";
    }
    
    if($type == 'register') {
      $recipient = $user->get('email');
      $subject = '';
      $templateName = "Signup";
      $templateSearches = array(
        ''
      );
      $templateReplacements = array(
        ''
      );

    } else if($type == 'forgotpassword') {
      $recipient = $user->get('email');
      $subject = "";
      $templateName = "ForgotPassword";
      $templateSearches = array(
        ''
      );
      $templateReplacements = array(
        ''
      );

    } else if($type == 'approval') {
      $recipient = $user->get('email');
      $subject = "";
      $templateName = "Approved";
      $templateSearches = array(
        ''
      );
      $templateReplacements = array(
        ''
      );

    } else {
      return "Invalid email type.";
    }

    if(!file_exists("{$SITE['path']}templates/$templateName.html") || !file_exists("{$SITE['path']}templates/$templateName.txt")) {
      return "Email templates do not exist.";
    }

    $textContent = file_get_contents("{$SITE['path']}templates/$templateName.txt");
    $htmlContent = file_get_contents("{$SITE['path']}templates/$templateName.html");

    if($htmlContent === false || $textContent === false) {
      return "Error loading email templates.";
    }

    $htmlContent = str_replace($templateSearches, $templateReplacements, $htmlContent);
    $textContent = str_replace($templateSearches, $templateReplacements, $textContent);

    $result = $this->create(
      array(
        'user_id' => $user->get('id'),
        'email' => $recipient,
        'subject' => $subject,
        'html' => $htmlContent,
        'text' => $textContent,
        'type' => $type,
        'status' => 'pending',
        'updated' => time(),
        'created' => time(),
      )
    );

    if(get_class($result) !== 'Email') {
      return $result;
    }

    return $this->load($result->get('id'));
  }
  
  public function sendMail() {
    global $MAIL, $DEPLOYED;

    if(!$DEPLOYED) {
      return $this;
    }

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
    $mail->Subject    = $this->get('subject');
    $mail->Body       = $this->get('html');
    $mail->AltBody    = $this->get('text');
    $mail->WordWrap   = 50;
    $mail->AddAddress($this->get('email'));
    $mail->IsHTML(true);

    $result = $mail->Send();
    if($result !== true) {
      return $result;
    } else {
      return $this->set('status', 'sent')->save();
    }
  }
  
  public static function getUnsentEmails() {
    $query = "SELECT id FROM emails WHERE status<>'sent'";
    $select = mysql_query($query);
    $array = array();
    while($temp = mysql_fetch_array($select)) {
      $array[] = $temp[0];
    }
    return $array;
  }
  
}

?>