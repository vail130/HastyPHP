<?PHP

class Email extends Model {

  public
    $class = 'Email',
    $table = 'emails',
    $fields = array(
      'id' => array('type' => 'int'),
      'user_id' => array('type' => 'int', 'min' => 0),
      'recipient' => array('type' => 'string', 'max' => 255, 'min' => 1),
      'subject' => array('type' => 'string', 'max' => 255, 'min' => 1),
      'html' => array('type' => 'string'),
      'text' => array('type' => 'string'),
      'type' => array('type' => 'string', 'max' => 20, 'min' => 1),
      'status' => array('type' => 'string', 'max' => 10, 'min' => 1, 'options' => array('pending', 'sent')),
      'updated' => array('type' => 'int'),
      'created' => array('type' => 'int'),
    );

  public function createRecord($attributes) {
    global $SETTINGS;

    switch($attributes['type']) {
      case 'account-created':
        $subject = 'Verify your email address';
        $templateSearches = array(
          '#Code#',
          '#Username#',
          '#Email#',
          '#BaseURL#',
          '#AppName#',
          '#RequestID#',
          '#UserID#',
        );
        $templateReplacements = array(
          $attributes['code'],
          $attributes['user']->get('username'),
          $attributes['user']->get('email'),
          $SETTINGS['BASE_URL'],
          $SETTINGS['SITE_NAME'],
          $attributes['request_id'],
          $attributes['user']->get('id'),
        );
        break;
      case 'account-activated':
        $subject = '';
        $templateSearches = array(
          '#Username#',
          '#Email#',
          '#BaseURL#',
          '#AppName#',
        );
        $templateReplacements = array(
          $attributes['user']->get('username'),
          $attributes['user']->get('email'),
          $SETTINGS['BASE_URL'],
          $SETTINGS['SITE_NAME'],
        );
        break;
    }

    if(!file_exists("{$SETTINGS['PATH']}templates/{$attributes['type']}.html") || !file_exists("{$SETTINGS['PATH']}templates/{$attributes['type']}.txt")) {
      return "Email templates do not exist.";
    }

    $textContent = file_get_contents("{$SETTINGS['PATH']}templates/{$attributes['type']}.txt");
    $htmlContent = file_get_contents("{$SETTINGS['PATH']}templates/{$attributes['type']}.html");

    if($htmlContent === false || $textContent === false) {
      return "Error loading email templates.";
    }

    $htmlContent = str_replace($templateSearches, $templateReplacements, $htmlContent);
    $textContent = str_replace($templateSearches, $templateReplacements, $textContent);

    $result = $this->create(
      array(
        'user_id' => $attributes->get('user_id'),
        'recipient' => $attributes['user']->get('email'),
        'subject' => $subject,
        'html' => $htmlContent,
        'text' => $textContent,
        'type' => $attributes['type'],
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
    global $SETTINGS;

    if($SETTINGS['DEPLOYMENT'] == 'local') {
      return $this;
    }

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPSecure = $SETTINGS['EMAIL_SECURE'];
    $mail->SMTPAuth   = true;
    $mail->Host       = $SETTINGS['EMAIL_HOST'];
    $mail->Port       = $SETTINGS['EMAIL_POST'];
    $mail->Username   = $SETTINGS['EMAIL_USER'];
    $mail->Password   = $SETTINGS['EMAIL_PASSWORD'];
    $mail->From       = $SETTINGS['EMAIL_FROM'];
    $mail->FromName   = $SETTINGS['EMAIL_FROM_NAME'];
    $mail->Subject    = $this->get('subject');
    $mail->Body       = $this->get('html');
    $mail->AltBody    = $this->get('text');
    $mail->WordWrap   = 50;
    $mail->AddAddress($this->get('recipient'));
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