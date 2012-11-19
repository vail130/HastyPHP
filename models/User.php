<?PHP

class User extends Model {

  public
    $class = 'User',
    $table = 'users',
    $fields = array(
      'id' => array('type' => 'int'),
      'email' => array('type' => 'string', 'min' => 1),
      'firstname' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'lastname' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'organization' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'street_1' => array('type' => 'string', 'max' => 100, 'min' => 0),
      'street_2' => array('type' => 'string', 'max' => 100, 'min' => 0),
      'city' => array('type' => 'string', 'max' => 100, 'min' => 0),
      'state' => array('type' => 'string', 'max' => 100, 'min' => 0),
      'zip' => array('type' => 'string', 'max' => 20, 'min' => 0),
      'hash' => array('type' => 'string'),
      'salt' => array('type' => 'string'),
      'type' => array('type' => 'string', 'max' => 20, 'min' => 0, 'options' => array('administrator', 'user')),
      'status' => array('type' => 'string', 'max' => 20, 'min' => 0, 'options' => array('pending', 'approved', 'ignored')),
      'updated' => array('type' => 'int'),
      'created' => array('type' => 'int'),
    );

  public function isAdmin() {
    return in_array($this->get('type'), array('administrator'));
  }

  public static function getRecords() {
    $query = "SELECT * FROM users WHERE type='user'";
    $select = mysql_query($query);
    $array = array();
    while($temp = mysql_fetch_array($select, MYSQL_ASSOC)) {
      $array[] = $temp;
    }
    return $array;
  }

  public function getRecord() {
    return array(
      'id' => $this->get('id'),
      'email' => $this->get('email'),
      'firstname' => $this->get('firstname'),
      'lastname' => $this->get('lastname'),
      'organization' => $this->get('organization'),
      'street_1' => $this->get('street_1'),
      'street_2' => $this->get('street_2'),
      'city' => $this->get('city'),
      'state' => $this->get('state'),
      'zip' => $this->get('zip'),
      'type' => $this->get('type'),
      'status' => $this->get('status'),
      'updated' => $this->get('updated'),
      'created' => $this->get('created'),
    );
  }

  public static function emailExists($e) {
    $e = mb_strtolower(mysql_real_escape_string($e));
    $query = "SELECT id FROM users WHERE LOWER(email)='$e'";
    return mysql_num_rows(mysql_query($query)) == 1;
  }
  
  public static function getUserIDByEmail($e) {
    $e = mysql_real_escape_string(mb_strtolower($e));
    $query = "SELECT id FROM users WHERE LOWER(email)='$e'";
    $select = mysql_query($query);
    if(mysql_num_rows($select) > 0) {
      $array = mysql_fetch_array($select);
      return (int)$array[0];
    } else {
      return false;
    }
  }
  
  public static function createHashAndSaltFromInput($p) {
    $bcrypt = new Bcrypt(15);
    $cryptArray = $bcrypt->createHashAndSaltFromInput($p);
    $hash = $cryptArray['hash'];
    $salt = $cryptArray['salt'];
    return $bcrypt->verify($p, $salt, $hash) === true ? $cryptArray : false;
  }
  
  public static function getHashFromInputAndSalt($p, $salt) {
    $bcrypt = new Bcrypt(15);
    $hash = $bcrypt->getHashFromInputAndSalt($p, $salt);
    return $bcrypt->verifyInputAndSaltWithHash($p, $salt, $hash) === true ? $hash : false;
  }

  public function validateRecord($attributes) {
    # Email must be valid and unused
    if(preg_match("/^[\w\.%\+-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}(?:\.[A-Z]{2,4})?$/i", $attributes['email']) == 0) {
      return "Invalid email address.";
    }
    if(User::emailExists($attributes['email'])) {
      return "Email address {$attributes['email']} is already associated with an account.";
    }

    return true;
  }

  public function createRecord($attributes) {
    $validation = $this->validateRecord($attributes);
    if($validation !== true) {
      return $validation;
    }

    $cryptArray = User::createHashAndSaltFromInput($attributes['password']);
    if($cryptArray === false) {
      return "Password encryption failed.";
    }

    $attributes['hash'] = $cryptArray['hash'];
    $attributes['salt'] = $cryptArray['salt'];

    $result = $this->create(
      array_merge(
        $attributes,
        array(
          'type' => 'user',
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

    $email = new Email();
    $result = $email->createRecord($this, 'register');
    if(get_class($result) === 'Email') {
      $email->sendMail();
    }

    return $this;
  }

  public function updateRecord($attributes) {
    $status = $attributes['status'];

    $result = $this->setAttributes(
      array(
        'status' => $attributes['status'],
        'updated' => time(),
      )
    )->save();

    if(get_class($result) !== 'User') {
      return $result;
    }

    if($status !== 'approved' && $this->get('status') === 'approved') {
      $email = new Email();
      $result = $email->createRecord($this, 'approval');
      if(get_class($result) === 'Email') {
        $email->sendMail();
      }
    }

    return $this;
  }

  public function deleteRecord() {
    $result = $this->set('status', 'deleted')->save();

    if(get_class($result) !== $this->class) {
      return $result;
    }

    return $this;
  }

  public static function searchUsers($s='', $no=array()) {
    $insert = '';
    for($i = 0; !empty($no[$i]); $i++) {
      $insert .= " AND name<>'".$no[$i]."'";
    }
    $query = "SELECT DISTINCT id FROM users WHERE name LIKE '%$s%' $insert";
    $select = mysql_query($query);
    $result = array();
    while($temp = mysql_fetch_array($select)) {
      $result[] = $temp[0];
    }
    return $result;
  }

}

?>