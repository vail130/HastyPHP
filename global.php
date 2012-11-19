<?PHP

global $SETTINGS;

$SETTINGS['SITE_NAME'] = 'HastyPHP';

$SETTINGS['DEPLOYMENT'] = 'local';

switch($SETTINGS['DEPLOYMENT']) {
  case 'web':
    $SETTINGS['BASE_URL'] = "";
    $SETTINGS['PATH'] = "";
    $SETTINGS['DATABASE_HOST'] = '';
    break;

  case 'local':
    $SETTINGS['BASE_URL'] = "http://localhost:8888/";
    $SETTINGS['PATH'] = "";
    $SETTINGS['DATABASE_HOST'] = 'localhost';
    break;
}

$SETTINGS['INDEX'] = "{$SETTINGS['PATH']}app.php";

date_default_timezone_set('America/New_York');

$SETTINGS['ADMIN_CODE'] = '';

$SETTINGS['DATABASE_NAME'] = '';
$SETTINGS['DATABASE_USER'] = '';
$SETTINGS['DATABASE_PASSWORD'] = '';

$SETTINGS['EMAIL_SECURITY'] = '';
$SETTINGS['EMAIL_HOST'] = '';
$SETTINGS['EMAIL_POST'] = '';
$SETTINGS['EMAIL_USER'] = '';
$SETTINGS['EMAIL_PASSWORD'] = '';
$SETTINGS['EMAIL_FROM'] = '';
$SETTINGS['EMAIL_FROM_NAME'] = "";

require("{$SETTINGS['PATH']}php-lib/phpMailer/class.phpmailer.php");
require("{$SETTINGS['PATH']}php-lib/WideImage/WideImage.php");
require("{$SETTINGS['PATH']}php-lib/lessc.inc.php");
require("{$SETTINGS['PATH']}php-lib/cssmin.php");
require("{$SETTINGS['PATH']}php-lib/jsmin.php");
require("{$SETTINGS['PATH']}php-lib/Bcrypt.php");

require("{$SETTINGS['PATH']}controllers/RouteController.php");
require("{$SETTINGS['PATH']}controllers/APIController.php");
require("{$SETTINGS['PATH']}controllers/DatabaseController.php");
require("{$SETTINGS['PATH']}controllers/SessionController.php");

require("{$SETTINGS['PATH']}models/Model.php");
require("{$SETTINGS['PATH']}models/Email.php");
require("{$SETTINGS['PATH']}models/User.php");
require("{$SETTINGS['PATH']}models/UserRequest.php");
require("{$SETTINGS['PATH']}models/Item.php");
require("{$SETTINGS['PATH']}models/ItemTag.php");
require("{$SETTINGS['PATH']}models/ItemImage.php");