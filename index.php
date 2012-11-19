<?PHP

require('global.php');

$dc = new DatabaseController();
$LINK = $dc->initialize();

$rc = new RouteController();
$rc->route();

$dc->terminate($LINK);