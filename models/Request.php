<?PHP

class Request extends Module {
	
	protected static
		$module_name = 'request',
		$table_name = 'requests',
		$allFields =
			array(
				array(
					'name' => 'status',
					'type' => 'string',
					'max' => 20,
					'min' => 0,
				),
				array(
					'name' => 'code',
					'type' => 'string',
					'max' => 255,
					'min' => 1,
				),
				array(
					'name' => 'request',
					'type' => 'string',
					'max' => 255,
					'min' => 0,
				),
				array(
					'name' => 'type',
					'type' => 'string',
					'max' => 20,
					'min' => 0,
				),
				array(
					'name' => 'user_id',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'updated',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
			),
		$limFields = array('typeArray'),
		$typeArray = array('email', 'register', 'password');
		
	public
		$created, $updated, $code, $request, $type, $user_id, $status;
	

	public static function isValidCode($code) {
		$code = mysql_real_escape_string($code);
		$query = "SELECT module_id FROM requests WHERE code='$code'";
		return mysql_num_rows(mysql_query($query)) >= 1;
	}
	
	public static function getRequestIDByCode($code) {
		$code = mysql_real_escape_string($code);
		$query = "SELECT module_id FROM requests WHERE code='$code' LIMIT 1";
		$select = mysql_query($query);
		if(mysql_num_rows($select) > 0) {
			$array = mysql_fetch_array($select);
			return (int)$array[0];
		}
		else {
			return false;
		}
	}
	
	public static function createRequest($uID, $type, $request='') {
		$code = Request::getUniqueName('code', 10);
		
		$rArray =
			array(
				'status' => 'pending',
				'code' => $code,
				'request' => $request,
				'type' => $type,
				'user_id' => $uID,
				'updated' => time(),
				'created' => time(),
			);
		return Request::create($rArray);
	}
}

?>