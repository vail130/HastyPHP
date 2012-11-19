<?PHP

class APIController {

  public function process($method, $params) {

    $pCount = count($params);
    if (stripos($params[$pCount - 1], '?') !== false) {
      $tempParam = explode('?', $params[$pCount - 1]);
      $params[$pCount - 1] = $tempParam[0];
    }

    switch($method) {
      case 'options':
        $this->serve(200);
        break;

      case 'post':
        if(empty($params[2]) || $params[2] !== 'upload') {
          $payload = json_decode(file_get_contents('php://input'), true);
          if(empty($payload)) {
            $this->serve(400, json_encode(array("json" => "Invalid JSON payload.")));
          }
          if(isset($payload['method']) && mb_strtolower($payload['method']) === 'put') {
            $method = 'put';
          }
        }
        break;

      case 'get':
        $payload = $_GET;
        if(isset($payload['method']) && mb_strtolower($payload['method']) === 'delete') {
          $method = 'delete';
        }
        break;

      default:
        $this->serve(400);
    }

    $validSession = SessionController::validSession();

    if($params[0] === null) {
      $this->serve(400);
    }

    # URL/api/items
    if($params[0] === 'items') {
      $user = new User();

      if (!empty($_SESSION['id'])) {
        $user->load($_SESSION['id']);
      }

      $item = new Item();

      # URL/api/items
      if(empty($params[1])) {
        switch ($method) {
          case 'get':
            $this->serve(200, json_encode(Item::getRecords($validSession)));
            break;

          case 'post':
            if(!$user->isAdmin()) {
              $this->serve(401);
            }

            $result = $item->createRecord((array)$payload['item']);
            if(get_class($result) === $item->class) {
              $this->serve(200, json_encode($item->getRecord()));
            } else {
              $this->serve(400, json_encode(array("item" => $result)));
            }
            break;

          case 'delete':
            if(!$user->isAdmin()) {
              $this->serve(401);
            }

            Item::deleteRecords($payload);
            $this->serve(200);
            break;

          case 'put':
            $this->serve(405);
            break;

          default:
            $this->serve(404);
        }
      }

      # URL/api/items/:id
      else if(empty($params[2])) {
        if(!$item->isValidID($params[1])) {
          $this->serve(404);
        }

        $item->load($params[1]);

        switch ($method) {
          case 'post':
            $this->serve(405);
            break;

          case 'get':
            $this->serve(200, json_encode($item->getRecord()));
            break;

          case 'put':
            if(!$user->isAdmin()) {
              $this->serve(401);
            }

            $result = $item->updateRecord((array)$payload['item']);
            if(get_class($result) === $item->class) {
              $this->serve(200, json_encode($item->getRecord()));
            } else {
              $this->serve(400, json_encode(array("item" => $result)));
            }
            break;

          case 'delete':
            if(!$user->isAdmin()) {
              $this->serve(401);
            }

            $result = $item->deleteRecord();
            if(get_class($result) === $item->class) {
              $this->serve(200);
            } else {
              $this->serve(400, json_encode(array("item" => $result)));
            }
            break;

          default:
            $this->serve(404);
        }
      }

      # URL/api/items/:id/upload
      else if($params[2] === 'upload') {
        if(!$user->isAdmin()) {
          $this->serve(401);
        }

        if(!$item->isValidID($params[1])) {
          $this->serve(404);
        }

        $item->load($params[1]);

        switch ($method) {
          case 'post':
            $image_key = null;
            $num = null;
            foreach($_FILES as $key => $value) {
              if(preg_match("/^image-(\d+)$/", $key, $matches) === 1) {
                $image_key = $key;
                $num = (int)$matches[1];
                break;
              }
            }

            if($image_key !== null && $num !== null) {
              $result = $item->createItemImage($_FILES[$image_key], $num);
              if(get_class($result) === $item->class) {
                $this->serve(200, json_encode($item->getRecord()));
              } else {
                $this->serve(400, json_encode(array("image" => $result)));
              }
            } else {
              $this->serve(400, json_encode(array("image" => "Invalid key.")));
            }
            break;

          case 'delete':
            if(!$user->isAdmin()) {
              $this->serve(401);
            }

            if(!isset($_GET['index'])) {
              $this->serve(400, json_encode(array("index" => "Missing image index.")));
            }

            $index = (int)$_GET['index'];
            if(!($index >= 0 && $index <= 3)) {
              $this->serve(400, json_encode(array("index" => "Invalid image index.")));
            }

            $result = $item->deleteItemImages($index);
            if(get_class($result) === $item->class) {
              $this->serve(200);
            } else {
              $this->serve(400, json_encode(array("image" => $result)));
            }
            break;

          default:
            $this->serve(404);
        }
      }
    }

    # URL/api/accounts
    else if($params[0] === 'accounts') {
      $user = new User();

      # URL/api/accounts
      if(empty($params[1])) {
        switch ($method) {
          case 'get':

            if(isset($_GET['forgotpassword']) && !empty($_GET['email'])) {
              $user_id = User::getUserIDByEmail(urldecode($_GET['email']));
              if($user_id === false) {
                $this->serve(400, json_encode(array('email' => 'Invalid email address.')));
              }

              $user = new User($user_id);
              $email = new Email();
              $result = $email->createRecord($user, 'forgotpassword');
              if(get_class($result) !== 'Email') {
                $this->serve(400, json_encode(array('email' => $result)));
              }

              $result = $email->sendMail();
              if(get_class($result) !== 'Email') {
                $this->serve(400, json_encode(array('email' => $result)));
              }

              $this->serve(200);

            } else {
              if(!$validSession) {
                $this->serve(401);
              }

              $user = new User(SessionController::getSessionID());
              if(!$user->isAdmin()) {
                $this->serve(401);
              }

              $this->serve(200, json_encode(User::getRecords()));
            }
            break;

          case 'post':
            $result = $user->createRecord($payload);
            if(get_class($result) === 'User') {
              $this->serve(200, json_encode($user->getRecord()));
            } else {
              $this->serve(400, json_encode(array("user" => $result)));
            }
            break;

          case 'delete':
            $this->serve(405);
            break;

          case 'put':
            $this->serve(405);
            break;

          default:
            $this->serve(404);
        }
      }

      # URL/api/accounts/:id
      else {
        if(!$validSession) {
          $this->serve(401);
        }

        if(!$user->isValidID($params[1])) {
          $this->serve(404);
        }

        $user->load($params[1]);

        switch ($method) {
          case 'post':
            $this->serve(405);
            break;

          case 'get':
            $this->serve(200, json_encode($user->getRecord()));
            break;

          case 'put':
            $admin = new User(SessionController::getSessionID());
            if(!$admin->isAdmin()) {
              $this->serve(401);
            }

            if (empty($payload['account'])) {
              $this->serve(400);
            }

            $result = $user->updateRecord($payload['account']);
            if(get_class($result) === 'User') {
              $this->serve(200, json_encode($user->getRecord()));
            } else {
              $this->serve(400, json_encode(array("user" => $result)));
            }
            break;

          case 'delete':
            $admin = new User();
            if(!$admin->isAdmin()) {
              $this->serve(401);
            }

            $result = $user->deleteRecord();
            if(get_class($result) === 'User') {
              $this->serve(200);
            } else {
              $this->serve(400, json_encode(array("user" => $result)));
            }
            break;

          default:
            $this->serve(404);
        }
      }
    }

    # URL/api/tags
    else if($params[0] === 'tags') {
      switch ($method) {
        case 'get':
          $this->serve(200, json_encode(ItemTag::getUniqueRecords($validSession)));
          break;

        default:
          $this->serve(405);
      }
    }

    # URL/api/session
    else if($params[0] === 'session') {
      switch($method) {
        case 'get':

          if (isset($_GET['email']) && isset($_GET['password'])) {
            $payload = array(
              'email' => $_GET['email'],
              'password' => $_GET['password'],
            );
            $result = SessionController::createSession($payload);
            if($result === true) {
              $this->serve(200, json_encode($_SESSION));
            } else {
              $this->serve(400, json_encode(array("session" => $result)));
            }
          } else {
            $this->serve(200, json_encode($_SESSION));
          }
          break;

        case 'post':
          if($validSession) {
            $this->serve(200, json_encode($_SESSION));
          }

          $result = SessionController::createSession($payload);
          if($result === true) {
            $this->serve(200, json_encode($_SESSION));
          } else {
            $this->serve(400, json_encode(array("session" => $result)));
          }
          break;

        case 'delete':
          SessionController::destroySession();
          $this->serve(200);
          break;

        default:
          $this->serve(405);
      }
    }

    else {
      $this->serve(404);
    }

  }

  private function serve($status, $data=null) {
    header(' ', true, $status);
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
    header('Access-Control-Expose-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD');
    header('Access-Control-Allow-Headers: origin, content-type, accept');
    header('Access-Control-Max-Age: 3600');
    header('Content-type: application/json');
    header('Cache-Control: max-age=3600, must-revalidate, private');
    if($data !== null) {
      echo $data;
    }
    exit;
  }

}