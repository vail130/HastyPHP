<?PHP

class UserReferralCode extends Module {
	
	protected static
		$module_name = 'userreferralcode',
		$table_name = 'userreferralcodes',
		$allFields =
			array(
				array(
					'name' => 'created',
					'type' => 'int',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'code',
					'type' => 'string',
					'max' => -1,
					'min' => 0,
				),
				array(
					'name' => 'user_id',
					'type' => 'int',
					'max' => -1,
					'min' => 1,
				),
			);
		
	public
		$created, $user_id, $code;
	
	public static function isValidCode($code) {
		$code = mysql_real_escape_string($code);
		$query = "SELECT module_id FROM userreferralcodes WHERE code='$code' LIMIT 1";
		return mysql_num_rows(mysql_query($query)) == 1;
	}
	
	public static function getUserIDByReferralCode($code) {
		$code = mysql_real_escape_string($code);
		$query = "SELECT user_id FROM userreferralcodes WHERE code='$code' LIMIT 1";
		$select = mysql_query($query);
		if(mysql_num_rows($select) > 0) {
			$array = mysql_fetch_array($select);
		}
		return !empty($array[0]) ? (int)$array[0] : false;
	}
	
	public static function getReferralCodeByUserID($uID) {
		$uID = mysql_real_escape_string($uID);
		$query = "SELECT code FROM userreferralcodes WHERE user_id='$uID' LIMIT 1";
		$select = mysql_query($query);
		if(mysql_num_rows($select) > 0) {
			$array = mysql_fetch_array($select);
		}
		return !empty($array[0]) ? $array[0] : false;
	}
	
}

?>