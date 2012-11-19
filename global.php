<?PHP

global $SITE, $MAIL, $DEPLOYED;

$DEPLOYED = false;

$deployment = 'local';

switch($deployment) {
  case 'web':
    $SITE['url'] = "";
    $SITE['path'] = "";
    $SITE['dbhost'] = '';
    break;

  case 'local':
    $SITE['url'] = "http://localhost:8888/";
    $SITE['path'] = "";
    $SITE['dbhost'] = 'localhost';
    break;
}

$SITE['libPath'] = $SITE['path'].'php-lib/';
$SITE['models'] = $SITE['path'].'models/';
$SITE['controllers'] = $SITE['path'].'controllers/';
$SITE['index'] = $SITE['path'].'index.html';

date_default_timezone_set('America/New_York');

$SITE['dbname'] = '';
$SITE['dbuser'] = '';
$SITE['dbpass'] = '';

$MAIL['secure'] = '';
$MAIL['host'] = '';
$MAIL['port'] = '';
$MAIL['user'] = '';
$MAIL['pass'] = '';
$MAIL['from'] = '';
$MAIL['fromName'] = "";

require($SITE['libPath'].'phpMailer/class.phpmailer.php');
require($SITE['libPath'].'WideImage/WideImage.php');

require($SITE['controllers'].'RouteController.php');
require($SITE['controllers'].'APIController.php');
require($SITE['controllers'].'DatabaseController.php');
require($SITE['controllers'].'SessionController.php');

require($SITE['models'].'Model.php');
require($SITE['models'].'Email.php');
require($SITE['models'].'User.php');
require($SITE['models'].'Item.php');
require($SITE['models'].'ItemTag.php');
require($SITE['models'].'ItemImage.php');

?>