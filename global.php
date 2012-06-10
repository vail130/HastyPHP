<?PHP

# Site name
global $SITE;

$SITE['name'] = 'HastyPHP';

# Web
#
$SITE['url'] = "http://{$SITE['name']}.com/";
$SITE['path'] = "/server-path-to-files/{$SITE['name']}/";

# Local
#
$SITE['url'] = "http://localhost/{$SITE['name']}/";
$SITE['path'] = "/local-path-to-files/{$SITE['name']}/";

$SITE['displayName'] = $SITE['name'];
$SITE['tagLine'] = '';

$SITE['title'] = $SITE['displayName'].' | '.$SITE['tagLine'];
$SITE['description'] = "";

$SITE['img'] = $SITE['url'].'img/';
$SITE['css'] = $SITE['url'].'css/';
$SITE['js'] = $SITE['url'].'js/';
$SITE['jsUtil'] = $SITE['js'].'util/';

$SITE['keywords'] = "";
$SITE['favicon'] = $SITE['img'].'favicon.ico';
$SITE['appleIcon'] = $SITE['img'].'icon-150.png';
$SITE['appleIcon72'] = $SITE['img'].'icon-72.png';
$SITE['appleIcon114'] = $SITE['img'].'icon-114.png';
$SITE['thumbnail'] = $SITE['img'].'icon-150.png';

$SITE['libPath'] = $SITE['path'].'php-lib/';
$SITE['models'] = $SITE['path'].'models/';
$SITE['controllers'] = $SITE['path'].'controllers/';
$SITE['views'] = $SITE['path'].'views/';

# ReCaptcha Keys
$SITE['publicKey'] = '';
$SITE['privateKey'] = '';

require($SITE['libPath'].'phpMailer/class.phpmailer.php');
require($SITE['libPath'].'recaptchalib.php');
require($SITE['libPath'].'WideImage/WideImage.php');
require($SITE['libPath'].'lessc.inc.php');
require($SITE['libPath'].'jsmin.php');
require($SITE['libPath'].'cssmin.php');
require($SITE['libPath'].'Browser.php');
require($SITE['libPath'].'Bcrypt.php');
require($SITE['libPath'].'Mobile_Detect.php');

require($SITE['controllers'].'RouteController.php');
require($SITE['controllers'].'DatabaseController.php');
require($SITE['controllers'].'SessionController.php');
require($SITE['controllers'].'RequestController.php');
require($SITE['controllers'].'RegisterController.php');
require($SITE['controllers'].'ForgotPasswordController.php');
require($SITE['controllers'].'ResetPasswordController.php');

require($SITE['models'].'Module.php');
require($SITE['models'].'Email.php');
require($SITE['models'].'Contact.php');
require($SITE['models'].'User.php');
require($SITE['models'].'Request.php');
require($SITE['models'].'UserReferralCode.php');
require($SITE['models'].'ReferredUser.php');
	
date_default_timezone_set('America/New_York');

# Database info
$SITE['dbhost'] = 'localhost';
$SITE['dbname'] = '';
$SITE['dbuser'] = '';
$SITE['dbpass'] = '';

# Mail
global $MAIL;

$MAIL['secure'] = '';
$MAIL['host'] = 'mail.'.$SITE['name'].'.com';
$MAIL['port'] = '';
$MAIL['user'] = 'no-reply@'.$SITE['name'].'.com';
$MAIL['pass'] = '';
$MAIL['from'] = 'no-reply@'.$SITE['name'].'.com';
$MAIL['fromName'] = $SITE['displayName'];
$MAIL['contact'] = 'contact@'.$SITE['name'].'.com';
$MAIL['contactPass'] = '';

?>