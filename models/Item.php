<?PHP

class Item extends Model {

  public
    $class = 'Item',
    $table = 'items',
    $fields = array(
      'id' => array('type' => 'int'),
      'num' => array('type' => 'int'),
      'title' => array('type' => 'string', 'max' => 100, 'min' => 0),
      'updated' => array('type' => 'int'),
      'created' => array('type' => 'int'),
    );

  public function createRecord($payload) {
    $this->deleteEmptyRecords();

    $result = $this->create(
      array_merge(
        $payload,
        array(
          'updated' => time(),
          'created' => time(),
        )
      )
    );

    if(get_class($result) !== $this->class) {
      return $result;
    }

    $this->load($result->get('id'));

    if(isset($payload['tags'])) {
      $this->createItemTags($payload['tags']);
    }

    return $this;
  }

  private function deleteEmptyRecords() {
    $query = "
      DELETE FROM items i
      LEFT JOIN itemimages im ON i.id=im.item_id
      LEFT JOIN itemtags it ON i.id=it.item_id
      WHERE i.updated=i.created AND im.item_id IS NULL AND it.item_id IS NULL
      ";
    mysql_query($query);
    return $this;
  }

  public function createItemTags($tags) {
    $itemTag = new ItemTag();

    for($i = 0; !empty($tags[$i]); $i++) {
      $tag = $tags[$i];
      if(isset($tag['tag']) && isset($tag['type'])) {
        $itemTag->createRecord(
          array(
            'item_id' => $this->get('id'),
            'tag' => $tag['tag'],
            'type' => $tag['type'],
          )
        );
      }
    }

    return $this;
  }

  public function deleteItemTags() {
    $query = "DELETE FROM itemtags WHERE item_id='{$this->get('id')}'";
    return mysql_query($query) !== true ? mysql_error() : $this;
  }

  public function deleteItemImages($num=null) {
    global $SITE;

    if($num !== null) {
      $query = "SELECT id FROM itemimages WHERE item_id='{$this->get('id')}' AND num='$num' LIMIT 1";
    } else {
      $query = "SELECT id FROM itemimages WHERE item_id='{$this->get('id')}'";
    }

    $select = mysql_query($query);
    if(mysql_num_rows($select) > 0) {
      while($row = mysql_fetch_array($select)) {
        $itemImage = new ItemImage($row['id']);

        $temp_filename = $itemImage->get('name');
        $temp_ext = $itemImage->get('ext');

        if(file_exists("{$SITE['path']}media/$temp_filename.$temp_ext")) {
          unlink("{$SITE['path']}media/$temp_filename.$temp_ext");
        }
        if(file_exists("{$SITE['path']}media/s$temp_filename.$temp_ext")) {
          unlink("{$SITE['path']}media/s$temp_filename.$temp_ext");
        }

        $itemImage->delete();
        unset($itemImage);
      }
    }

    if($num !== null) {
      $query = "DELETE FROM itemimages WHERE item_id='{$this->get('id')}' AND num='$num'";
    } else {
      $query = "DELETE FROM itemimages WHERE item_id='{$this->get('id')}'";
    }

    return mysql_query($query) !== true ? mysql_error() : $this;
  }

  public function createItemImage($file, $num) {
    global $SITE;

    $this->deleteItemImages($num);

    $info = getimagesize($file['tmp_name']);
    $temp = explode('/', $info['mime']);
    $ext = $temp[1];

    if(!in_array($ext, array('jpg', 'jpeg', 'pjpeg', 'png', 'gif'))) {
      return "Invalid mimetype extension.";
    }

    if($ext == 'jpeg' || $ext == 'pjpeg') {
      $ext = 'jpg';
    }

    # Max file size 2MB
    if($file['size'] > 2097152) {
      return "Image must be under 2MB.";
    }

    $itemImage = new ItemImage();
    $filename = $itemImage->getUniqueName('name', 100);

    $img = WideImage::load($file['tmp_name']);
    $img->resize(1200, 800, 'inside', 'down')->saveToFile("{$SITE['path']}media/$filename.$ext");
    $img->resize(200, 200, 'outside', 'down')->crop('center','middle',200,200)->saveToFile("{$SITE['path']}media/s$filename.$ext");
    
    $data = getimagesize("{$SITE['path']}media/$filename.$ext");
    
    $itemImage->createRecord(
      array(
        'item_id' => $this->get('id'),
        'width' => $data[0],
        'height' => $data[1],
        'num' => $num,
        'name' => $filename,
        'ext' => $ext
      )
    );

    return $this->set('updated', time())->save();
  }

  public function updateRecord($payload) {
    $item = $this->setAttributes(
      array_merge(
        $payload,
        array(
          'created' => $this->get('created'),
          'updated' => time()
        )
      )
    )->save();

    if(get_class($item) !== $this->class) {
      return $item;
    }

    $this->deleteItemTags();

    if(isset($payload['tags'])) {
      $this->createItemTags($payload['tags']);
    }

    return $item;
  }

  public function deleteRecord() {
    $this->deleteItemTags($this->get('id'));
    $this->deleteItemImages();
    $result = $this->delete();
    if(get_class($result) !== $this->class) {
      return $result;
    }
    return $this;
  }

  public static function deleteRecords($idArray) {
    $item = new Item();
    for($i = 0; !empty($idArray[$i]); $i++) {
      $item->load($idArray[$i])->deleteRecord();
    }
  }

  public static function getRecords() {
    $query = "SELECT * FROM items";

    $select = mysql_query($query);

    if(mysql_num_rows($select) === 0) {
      return array();
    }

    $items = array();
    for($i = 0; $row = mysql_fetch_array($select, MYSQL_ASSOC); $i++) {
      $items[$i] = $row;
      $items[$i]['tags'] = ItemTag::getItemTags($row['id']);
      $items[$i]['images'] = ItemImage::getItemImages($row['id']);
    }

    return $items;
  }

  public function getRecord() {
    $query = "SELECT * FROM items WHERE id='{$this->get('id')}'";
    $select = mysql_query($query);

    if(mysql_num_rows($select) === 0) {
      return array();
    }

    return array_merge(
      mysql_fetch_array($select, MYSQL_ASSOC),
      array(
        'tags' => ItemTag::getItemTags($this->get('id')),
        'images' => ItemImage::getItemImages($this->get('id'))
      )
    );
  }
  
}

?>