<?PHP

class User extends Model {

  public
    $class = 'User',
    $table = 'users',
    $fields = array(
      'id' => array('type' => 'int'),
      'username' => array('type' => 'string', 'min' => 4),
      'email' => array('type' => 'string', 'min' => 5),
      'hash' => array('type' => 'string'),
      'salt' => array('type' => 'string'),
      'type' => array('type' => 'string', 'options' => array('administrator', 'user')),
      'status' => array('type' => 'string', 'options' => array('pending', 'active', 'deleted')),

      'first_name' => array('type' => 'string', 'max' => 100),
      'last_name' => array('type' => 'string', 'max' => 100),
      'organization' => array('type' => 'string', 'max' => 100),
      'street_1' => array('type' => 'string', 'max' => 100),
      'street_2' => array('type' => 'string', 'max' => 100),
      'city' => array('type' => 'string', 'max' => 100),
      'state' => array('type' => 'string', 'max' => 100),
      'zip' => array('type' => 'string', 'max' => 50),
      'phone' => array('type' => 'string', 'max' => 50),
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
      'username' => $this->get('username'),
      'email' => $this->get('email'),
      'first_name' => $this->get('first_name'),
      'last_name' => $this->get('last_name'),
      'organization' => $this->get('organization'),
      'street_1' => $this->get('street_1'),
      'street_2' => $this->get('street_2'),
      'city' => $this->get('city'),
      'state' => $this->get('state'),
      'zip' => $this->get('zip'),
      'phone' => $this->get('phone'),
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

  public static function getUserIDByUsername($u) {
    $u = mysql_real_escape_string(mb_strtolower($u));
    $query = "SELECT id FROM users WHERE LOWER(username)='$u'";
    $select = mysql_query($query);
    if(mysql_num_rows($select) > 0) {
      $array = mysql_fetch_array($select);
      return (int)$array[0];
    } else {
      return false;
    }
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

    $cryptArray = Model::createHashAndSaltFromInput($attributes['password']);
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

    $request = new UserRequest();
    $result = $request->createRecord(
      array(
        'user_id' => $this->get('id'),
        'type' => 'account-created',
        'request' => ''
      )
    );

    if(get_class($result) !== $request->class) {
      return $result;
    }

    $this->load($result->get('id'));

    return $this;
  }

  public function updateRecord($attributes) {
    $status = $this->get('status');

    if (isset($attributes['request_id']) && isset($attributes['code'])) {
      $request = new UserRequest($attributes['request_id']);
      if ($request->get('status') === 'pending') {
        $bcrypt = new Bcrypt(15);
        if ($bcrypt->verify($attributes['code'], $request->get('salt'), $request->get('hash'))) {
          $status = 'active';
          $result = $request->completeRequest();

          if(get_class($result) !== 'UserRequest') {
            return $result;
          }
        }
      }
    }

    $result = $this->setAttributes(
      array_merge(
        $attributes,
        array(
          'type' => $this->get('type'),
          'status' => $status,
          'updated' => time(),
          'created' => $this->get('created'),
        )
      )
    )->save();

    if(get_class($result) !== 'User') {
      return $result;
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