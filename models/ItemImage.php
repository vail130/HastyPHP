<?PHP

class ItemImage extends Model {

  public
    $class = 'ItemImage',
    $table = 'itemimages',
    $fields = array(
      'id' => array('type' => 'int'),
      'item_id' => array('type' => 'int', 'min' => 1),
      'name' => array('type' => 'string', 'max' => 100, 'min' => 1),
      'ext' => array('type' => 'string', 'max' => 5, 'min' => 1),
      'num' => array('type' => 'int', 'min' => 0),
      'width' => array('type' => 'int', 'min' => 0),
      'height' => array('type' => 'int', 'min' => 0),
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

  public static function getItemImages($item_id) {
    global $SITE;

    $query = "SELECT * FROM itemimages WHERE item_id='$item_id' ORDER BY num ASC";
    $select = mysql_query($query);

    $images = array();
    if(mysql_num_rows($select) === 0) {
      return $images;
    }

    for($i = 0; $row = mysql_fetch_array($select, MYSQL_ASSOC); $i++) {
      $images[$i] = array(
        "index" => $row['num'],
        "width" => $row['width'],
        "height" => $row['height'],
        "url" => $SITE['url'].'media/'.$row['name'].'.'.$row['ext'],
        "thumbnail" => $SITE['url'].'media/s'.$row['name'].'.'.$row['ext'],
        "created" => $row['created'],
      );
    }

    return $images;
  }

}

?>