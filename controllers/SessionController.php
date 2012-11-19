<?PHP

class SessionController {

  public function __construct() {

  }

  public static function createSession($attributes) {
    if(empty($attributes['email'])) {
      return "Missing email address.";
    }
    if(empty($attributes['password'])) {
      return "Missing password.";
    }

    $uID = User::getUserIDByEmail($attributes['email']);
    if($uID === false) {
      return "Invalid user name.";
    }

    $user = new User($uID);
    if($user->get('type') !== 'administrator' && $user->get('status') !== 'approved') {
      return "Unauthorized account.";
    }

    $hash = User::getHashFromInputAndSalt($attributes['password'], $user->get('salt'));
    if($user->get('hash') !== $hash) {
      return "Invalid password.";
    }

    session_unset();
    session_destroy();
    session_start();
    $_SESSION['id'] = (int)$user->get('id');
    $_SESSION['email'] = $user->get('email');
    $_SESSION['type'] = $user->get('type');
    return true;
  }

  public static function destroySession() {
    $_SESSION['id'] = null;
    session_unset();
    session_destroy();
  }

  public static function validSession() {
    //check to make sure the session variable is registered
    $user = new User();
    return !empty($_SESSION['id']) && $user->isValidID($_SESSION['id']);
  }

  public static function getSessionType() {
    //check to make sure the session variable is registered 
    return isset($_SESSION['type']) ? $_SESSION['type'] : false;
  }

  public static function getSessionID() {
    return (int)$_SESSION['id'];
  }

}

?>