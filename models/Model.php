<?PHP

class Model {
    
  public
    $class, $table,
    $fields = array(),
    $attributes = array();
  
  public function __construct($id=0) {
    return $id === 0 ? $this : $this->load((int)$id);
  }

  public function validate($attributes) {
    foreach($this->fields as $key => $value) {
      $name = $key;
      $type = !empty($value['type']) ? mb_strtolower($value['type']) : null;
      $attr = !empty($attributes[$key]) ? $attributes[$key] : null;
      if($attr !== null && $type !== null) {
        $options = !empty($value['options']) ? $value['options'] : null;
        $min = !empty($value['min']) ? $value['min'] : null;
        $max = !empty($value['max']) ? $value['max'] : null;

        if($type == 'string') {
          $attr = (string)$attr;
          if(is_numeric($min) && strlen($attr) < $min) {
            return "value for $name is too short (min = $min) ($attr)";
          }
          if(is_numeric($max) && strlen($attr) > $max) {
            return "value for $name is too long (max = $max) ($attr)";
          }
        } else if($type == 'int' || $type == 'float') {
          if($type == 'int') {
            $attr = (int)$attr;
          } else if($type == 'float') {
            $attr = (float)$attr;
          }
          if(is_numeric($min) && $attr < $min) {
            return "value for $name is too small (min = $min) ($attr)";
          }
          if(is_numeric($max) && $attr > $max) {
            return "value for $name is too big (max = $max) ($attr)";
          }
        }

        if($options !== null) {
          if(!in_array($attr, $options)) {
            return "Invalid attribute value for key $name";
          }
        }
      }
    }
    return true;
  }

  public function create($attributes) {
    $fText = '';
    $vText = '';
    foreach($this->fields as $key => $value) {
      $fText .= mysql_real_escape_string($key).", ";
      if(!empty($attributes[$key])) {
        $vText .= "'".mysql_real_escape_string($attributes[$key])."', ";
      } else {
        $vText .= "'', ";
      }
    }
    # Trim off last comma and space
    $fText = substr($fText, 0, strlen($fText)-2);
    $vText = substr($vText, 0, strlen($vText)-2);
    $query = "INSERT INTO {$this->table} ($fText) VALUES ($vText)";

    if(mysql_query($query) !== true) {
      return mysql_error();
    }

    return $this->load(mysql_insert_id());
  }

  public function delete() {
    $query = "DELETE FROM {$this->table} WHERE id='{$this->get('id')}'";
    if(mysql_query($query) !== true) {
      return mysql_error();
    }
    return $this;
  }
  
  public function load($id) {
    $id = mysql_real_escape_string($id);
    $query = "SELECT * FROM {$this->table} WHERE id='$id' LIMIT 1";
    $select = mysql_query($query);
    
    if(mysql_num_rows($select) === 0) {
      return $this;
    }
    
    return $this->setAttributes(mysql_fetch_array($select, MYSQL_ASSOC));
  }

  public function get($key) {
    return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
  }

  public function set($key, $value) {
    if(isset($this->fields[$key]) && isset($attributes[$key])) {
      $this->attributes[$key] = $value;
    }
    return $this;
  }

  public function setAttributes($attributes) {
    foreach($this->fields as $key => $value) {
      if(isset($attributes[$key])) {
        $this->attributes[$key] = $attributes[$key];
      }
    }
    return $this;
  }
  
  public function save() {
    $query = "UPDATE {$this->table} SET ";
    $temp = '';
    foreach($this->fields as $key => $value) {
      $temp .= !empty($temp) ? ', ' : '';
      $temp .= mysql_real_escape_string($key)."='".mysql_real_escape_string($this->get($key))."'";
    }
    if($temp == '') {
      return "Empty SQL query string.";
    }
    $query .= $temp." WHERE id='".$this->get('id')."'";
    $result = mysql_query($query);
    if($result !== true) {
      return mysql_error();
    }
    return $this;
  }
  
  public function getUniqueName($field, $numChars) {
    $field = mysql_real_escape_string($field);
    $numChars = (int)$numChars;
    $text = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $max = strlen($text) - 1;
    $fName = '';
    $nRows = 1;
    while($fName == '' || $nRows > 0) {
      $fName = '';
      for($i = 0; $i < $numChars; $i++) {
        $fName .= $text[mt_rand(0,$max)];
      }
      $query = "SELECT $field FROM {$this->table} WHERE $field LIKE '%$fName%'";
      $select = mysql_query($query);
      $nRows = mysql_num_rows($select);
    }
    return $fName;
  }
  
  public function isUniqueName($field, $value) {
    $field = mysql_real_escape_string($field);
    $value = mysql_real_escape_string($value);
    $query = "SELECT $field FROM {$this->table} WHERE $field LIKE '%$value%'";
    return mysql_num_rows(mysql_query($query)) == 0;
  }
  
  public function filterCount($array) {
    if(!is_array($array)) { return false; }
    sort($array);
    $data = array();
    $labels = array();
    for($i = 0, $j = 0; isset($array[$i]); $i++) {
      if($i == 0 || $array[$i] != $labels[$j]) {
        if($i > 0) {
          $j++;
        }
        $labels[$j] = $array[$i];
        $data[$j] = 1;
      }
      else {
        $data[$j]++;
      }
    }
    
    return array($labels, $data);
  }
  
  public function isValidID($id) {
    $id = mysql_real_escape_string($id);
    $query = "SELECT id FROM {$this->table} WHERE id='$id' LIMIT 1";
    return mysql_num_rows(mysql_query($query)) === 1;
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

  public static function formatTimeAgo($timestamp) {
    $minute = 60;
    $hour = $minute*60;
    $day = $hour*24;
    $week = $day*7;
    $month = $day*30;
    $timeAgo = time() - $timestamp;
    $plural = '';
    if($timeAgo > $month) {
      $timeDiff = round($timeAgo/$month);
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff." month$plural ago";
    }
    else if($timeAgo > $week) {
      $timeDiff = round($timeAgo/$week);
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff." week$plural ago";
    }
    else if($timeAgo > $day) {
      $timeDiff = ceil($timeAgo/$day);
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff." day$plural ago";
    }
    else if($timeAgo > $hour) {
      $timeDiff = ceil($timeAgo/$hour);
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff." hour$plural ago";
    }
    else if($timeAgo > $minute) {
      $timeDiff = ceil($timeAgo/$minute);
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff." minute$plural ago";
    }
    else {
      $timeDiff = $timeAgo;
      if($timeDiff > 1) {
        $plural = 's';
      }
      $output = $timeDiff."$plural ago";
    }
    return $output;
  }
}