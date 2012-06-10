<?PHP

class DatabaseController {
	
	# Initalize the database connection
	public function initialize() {
		global $SITE;
		$l = mysql_connect($SITE['dbhost'], $SITE['dbuser'], $SITE['dbpass']) or die("Error: ".mysql_error()); 
		mysql_select_db($SITE['dbname']) or die("Error: ".mysql_error());
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