<?PHP

class DatabaseController {
	
	# Initalize the database connection
	public function initialize() {
		global $SETTINGS;
		$l = mysql_connect($SETTINGS['DATABASE_HOST'], $SETTINGS['DATABASE_USER'], $SETTINGS['DATABASE_PASSWORD']) or die("Error: ".mysql_error());
		mysql_select_db($SETTINGS['DATABASE_NAME']) or die("Error: ".mysql_error());
		session_start();
		header("Cache-control: private");
		return $l;
	}
	
	# Close the database connection
	public function terminate($l) {
		return mysql_close($l) or die ("Error: ".mysql_error()); 
	}
	
}

?>