<?PHP

class Module {
		
	protected static
		$id_field = 'module_id',
		$module_name = 'module',
		$table_name = 'modules',
		$limFields = array(),
		$allFields = array();
	
	public
		$module_id = 0;
	
	public function __construct($id=0) {
		return $id === 0 ? $this : $this->populate((int)$id);
	}
	
	public static function create($moduleArray) {
		$count = count(static::$allFields);
		for($i = 0; $i < $count; $i++) {
			unset($value, $name, $min, $max, $type);
			$min = static::$allFields[$i]['min'];
			$max = static::$allFields[$i]['max'];
			$type = mb_strtolower(static::$allFields[$i]['type']);
			$name = static::$allFields[$i]['name'];
			$value = $moduleArray[$name];
			
			if($type == 'string') {
				if(isset($value) && !is_string($value)) {
					return "Error: invalid string for $name ($value)";
				}
				if(is_string($value) && strlen($value) < $min) {
					return "Error: value for $name is too short (min = $min) ($value)";
				}
				if($max > -1 && strlen($value) > $max) {
					return "Error: value for $name is too long (max = $max) ($value)";
				}
			}
			else if($type == 'int' || $type == 'float') {
				if(isset($value)) {
					if($type == 'int') {
						if(!is_numeric($value) || (int)$value != $value) {
							return "Error: invalid integer for $name ($value)";
						}
						else {
							$value = (int)$value;
						}
					}
					else if($type == 'float') {
						if(!is_numeric($value) || (float)$value != $value) {
							return "Error: invalid floating point number for $name ($value)";
						}
						else {
							$value = (float)$value;
						}
					}
				}
				if($value < $min) {
					return "Error: value for $name is too small (min = $min) ($value)";
				}
				if($max != -1 && $value > $max) {
					return "Error: value for $name is too big (max = $max) ($value)";
				}
			}
			else if($type == 'bool' || $type == 'boolean') {
				if($value !== true && $value !== false) {
					return "Error: value for $name must be true or false ($value)";
				}
			}
			# Make sure that the values of the fields with restricted options are valid
			$arrayName = $name.'Array';
			if(in_array($arrayName, static::$limFields) && !in_array($value, static::$$arrayName)) { 
				return "Error: invalid value for key $name";
			}
			$insertArray[$name] = $value;
		}
		$result = static::insertData($insertArray);
		return (is_int($result) ? new static::$module_name($result) : $result.'; '.mysql_error());
	}
	
	public function delete() {
		$idName = static::$id_field;
		$query = "DELETE FROM ".static::$table_name." WHERE ".static::$id_field."='{$this->module_id}'";
		$delete = mysql_query($query);
		$query = "OPTIMIZE TABLE ".static::$table_name;
		$optimize = mysql_query($query);
		return $this;
	}
	
	protected static function insertData($moduleArray) {
		$fText = '';
		$vText = '';
		# Run through the array
		foreach($moduleArray as $key => $value) {
			$fText .= mysql_real_escape_string($key).', ';
			$vText .= '\''.mysql_real_escape_string($value).'\', ';
		}
		# Trim off last comma and space (downside here to using foreach loop, but worth it for simplicity)
		$fText = substr($fText, 0, strlen($fText)-2);
		$vText = substr($vText, 0, strlen($vText)-2);
		$query = "INSERT INTO ".static::$table_name." ($fText) VALUES ($vText)";
		$insert = mysql_query($query);
		return $insert ? mysql_insert_id() : "Error: Could not insert module data into database.";
	}
	
	public function populate($id) {
		if(!static::isValidID((int)$id)) {
			return $this;
		}
		
		$id = mysql_real_escape_string($id);
		$query = "SELECT * FROM ".static::$table_name." WHERE ".static::$id_field."='$id'";
		$select = mysql_query($query);
		
		if(mysql_num_rows($select) == 0) {
			return $this;
		}
		
		$moduleArray = mysql_fetch_array($select);
		
		if(empty($moduleArray)) {
			return $this;
		}
		
		foreach($moduleArray as $key => $value) {
			$this->$key = $value;
		}
		return $this;
	}
	
	public function getRecord($column, $value, $get) {
		$get = mysql_real_escape_string($get);
		$query = "SELECT $get FROM {$this->table} WHERE ";
		
		if(is_array($column)) {
			foreach($column as $key => $val) {
				if($key > 0) {
					$query .= " AND ";
				}
				$query .= mysql_real_escape_string($val)."='".(is_array($value) ? mysql_real_escape_string($value[$key]) : mysql_real_escape_string($value))."' ";
			}
		}
		else {
			$column = mysql_real_escape_string($column);
			$value = mysql_real_escape_string($value);
			$query .= "$column='$value'";
		}
		
		$select = mysql_query($query);
		$array = mysql_fetch_array($select);
		return $array[0];
	}
	
	public function setRecord($column, $value) {
		if(!isset($this->$column)) {
			return 'No such column.';
		}
		$column = mysql_real_escape_string($column);
		$value = mysql_real_escape_string($value);
		$query = "UPDATE ".static::$table_name." SET $column='$value' WHERE ".static::$id_field."='{$this->module_id}'";
		$e = mysql_query($query);
		
		if(isset($this->updated)) {
			$query = "UPDATE ".static::$table_name." SET updated='".time()."' WHERE ".static::$id_field."='{$this->module_id}'";
			mysql_query($query);
		}
		
		return ($e === true ? $e : mysql_error());
	}
	
	public function setModule($array) {
		$query = "UPDATE ".static::$table_name." SET ";
		$temp = '';
		foreach($array as $key => $value) {
			if(!empty($temp)) {
				$temp .= ', ';
			}
			if(isset($this->$key)) {
				$temp .= mysql_real_escape_string($key)."='".mysql_real_escape_string($value)."'";
			}
		}
		if($temp == '') {
			return "Error: Empty SQL query string.";
		}
		$query .= $temp." WHERE module_id='{$this->module_id}'";
		$result = mysql_query($query);
		if($result !== true) {
			return mysql_error();
		}
		return $this;
	}
	
	public static function getUniqueName($field, $numChars) {
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
			$query = "SELECT $field FROM ".static::$table_name." WHERE $field LIKE '%$fName%'";
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
	
	public static function isValidID($id) {
		$id = mysql_real_escape_string($id);
		$query = "SELECT ".static::$id_field." FROM ".static::$table_name." WHERE ".static::$id_field."='$id' LIMIT 1";
		return mysql_num_rows(mysql_query($query)) === 1;
	}
	
	public static function enhanceText($t, $html=true) {
		$output = stripslashes($t);
		
		$output = preg_replace(
			'/<(head|style|script|object|embed|applet|noframes|noscript|noembed)*?(?:\/$1>|\/>)/i',
			'', $output
		);
	  
    	if($html === false) {
			$output = preg_replace(
				array(
					// Add line breaks before and after blocks
					'@</?((address)|(blockquote)|(center)|(del))@iu',
					'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
					'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
					'@</?((table)|(th)|(td)|(caption))@iu',
					'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
					'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
					'@</?((frameset)|(frame)|(iframe))@iu',
				),
				array("$0", "$0", "$0", "$0", "$0","$0", "$0"),
				$output
			);
			$output = strip_tags($output);
		}
		
		// some URLs will be inside attributes
		//		src='URL'
		// some URLs will be inside link tags
		//		<a ...>URL</a>
		// filter out these examples
		
		preg_match_all(
			'/(?:(?:https?:\/\/)|(?:www\.))(?:[a-z0-9-]+\.)+[a-z\.]{2,6}(?:[\/\w#%\?:;=&\.,!-]*)/i',
			$output, $tempMatches, PREG_OFFSET_CAPTURE
		);
		$offset = 0;
		$matches = $tempMatches[0];
		
		for($i = 0; isset($matches[$i]); $i++) {
			$matchPos = $matches[$i][1] + $offset;
			$matchLen = strlen($matches[$i][0]);
			
			# Get the last character in the current match of $output
			$last = substr($output, $matchPos-1, $matchPos);
			$A = false;
			
			// check if substring leading up to this match ends with <A> opening of tag
			if($last === '>' && preg_match('/<a(?:[^>]*?)>$/i', substr($output, 0, $matchPos)) > 0) {
				$A = true;
			}
			
			//	if match is immediately preceeded by single or double quote or opening <A> tag,
			//		don't add, because the matched URL is already in an <A> tag
			//	if last is a quotation mark, then we assume this url is the HREF attribute for
			// 		an <A> tag, which we also don't want
			if($last !== "'" && $last !== '"' && $A === false) {
				$httpInsert = '';
				if(substr($matches[$i][0], 0, 7) !== 'http://' && substr($matches[$i][0], 0, 8) !== 'https://') {
					$httpInsert = 'http://';
				}
				$insert = "<a rel='nofollow' class='link' target='_blank' href='$httpInsert{$matches[$i][0]}'>{$matches[$i][0]}</a>";
				$output = substr($output, 0, $matchPos).$insert.substr($output, $matchPos + $matchLen);
				$offset += strlen($insert) - $matchLen;
			}
		}
		$order = array("\r\n", "\n", "\r");
		$output = str_replace($order, '<br/>', $output);
		return $output;
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
			$output = $timeDiff."s ago";
		}
		return $output;
	}
}

?>