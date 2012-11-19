<?PHP

class ItemTag extends Model {

  public
    $class = 'ItemTag',
    $table = 'itemtags',
    $fields = array(
      'id' => array('type' => 'int'),
      'item_id' => array('type' => 'int', 'min' => 1),
      'tag' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'type' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'created' => array('type' => 'int'),
    );
  
  public function createRecord($attributes) {
    $result = $this->create(
      array_merge(
        $attributes,
        array(
          'created' => time(),
        )
      )
    );

    if(get_class($result) !== $this->class) {
      return $result;
    }
    return $this->load($result->get('id'));
  }

  public static function getItemTags($item_id) {
    $query = "SELECT * FROM itemtags WHERE item_id='$item_id'";
    $select = mysql_query($query);

    $tags = array();
    if(mysql_num_rows($select) === 0) {
      return $tags;
    }

    for($i = 0; $row = mysql_fetch_array($select, MYSQL_ASSOC); $i++) {
      $tags[$i] = $row;
    }

    return $tags;
  }

  public static function getUniqueRecords() {
    $query = "SELECT DISTINCT tag, type FROM itemtags";
    $select = mysql_query($query);

    $allTags = array();
    if(mysql_num_rows($select) === 0) {
      return $allTags;
    }

    while($row = mysql_fetch_array($select, MYSQL_ASSOC)) {
      if(empty($allTags[$row['type']])) {
        $allTags[$row['type']] = array();
      }
      $allTags[$row['type']][] = $row['tag'];
    }

    $tags = array();
    foreach($allTags as $key => $value) {
      $tags[$key] = array_values($value);
    }

    return $tags;
  }

}

?>