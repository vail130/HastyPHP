<?PHP

require('global.php');

$rc = new RouteController();
$PAGE = $rc->getPage();

$SESSION = SessionController::validSession();

// This won't work until a database is set up
#$dc = new DatabaseController();
#$LINK = $dc->initialize();

$PARAMS = $rc->route();

require("{$SITE['path']}views/template.php");

// This won't work until a database is set up
#$dc->terminate($LINK);

?>