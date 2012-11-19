<?PHP

class UserRequest extends Model {

  public
    $class = 'UserRequest',
    $table = 'userrequests',
    $fields = array(
      'id' => array('type' => 'int'),
      'user_id' => array('type' => 'int', 'min' => 1),
      'hash' => array('type' => 'string'),
      'salt' => array('type' => 'string'),
      'request' => array('type' => 'string'),
      'type' => array('type' => 'string'),
      'status' => array('type' => 'string', 'options' => array('pending', 'complete')),
      'updated' => array('type' => 'int'),
      'created' => array('type' => 'int'),
    );
  
  public function createRecord($attributes) {
    $code = $this->getUniqueName('hash', 32);
    $cryptArray = Model::createHashAndSaltFromInput($code);
    $attributes['hash'] = $cryptArray['hash'];
    $attributes['salt'] = $cryptArray['salt'];

    $result = $this->create(
      array_merge(
        $attributes,
        array(
          'status' => 'pending',
          'updated' => time(),
          'created' => time(),
        )
      )
    );

    if(get_class($result) !== $this->class) {
      return $result;
    }

    $this->load($result->get('id'));

    switch($this->get('type')) {
      case 'account-created':
        $user = new User($this->get('user_id'));
        $email = new Email();
        $result = $email->createRecord(
          array(
            'type' => 'account-created',
            'user' => $user,
            'code' => $code,
            'request_id' => $this->get('id'),
          )
        );

        if(get_class($result) === 'Email') {
          $email->sendMail();
        }
        break;
    }

    return $this;
  }

  public function completeRequest() {
    $this->set('status', 'complete')->save();

    switch($this->get('type')) {
      case 'account-activated':
        $user = new User($this->get('user_id'));
        $email = new Email();
        $result = $email->createRecord(
          array(
            'type' => 'account-activated',
            'user' => $user,
          )
        );

        if(get_class($result) === 'Email') {
          $email->sendMail();
        }
        break;
    }

    return $this;
  }

}