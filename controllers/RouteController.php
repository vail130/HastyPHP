<?PHP

class RouteController {
	
	private
		$url,
		$method,
		$availablePages,
		$page;

	public function __construct() {
		global $SITE;
		
		// This can likely be done in the .htaccess file...
		// Redirect all requests to http://www.DOMAIN.com to http://DOMAIN.com
		if(substr($_SERVER['SERVER_NAME'], 0, 4) === 'www.') {
			$path = !empty($_SERVER['ORIG_PATH_INFO']) ? substr($_SERVER['ORIG_PATH_INFO'], 1) : '';
			exit(header("location: {$SITE['url']}$path"));
		}
		
		$this->method = $_SERVER['REQUEST_METHOD'];
		$this->url = $this->parseURL();
	}
	
	private function parseURL() {
		#This grabs the page requested. It should look something like this: /__DIR__/user/tim
		$request = $_SERVER['REQUEST_URI'];
		#This gets the script name. It should look something like this: /__DIR__/index.php
		$filename = $_SERVER['SCRIPT_NAME'];
		#This removes the string "/__DIR__/" off the beginning of the request. It is not needed.
		$request = urldecode(substr($request, strrpos($filename, '/') + 1));
		
		#This removes all the trailing slashes off the request. It helps clean up the request.
		while(substr($request, -1) == '/') {
			$request = substr($request, 0, -1);
		}
		#This removes all the beginning slashes off the request. It helps clean up the request.
		while(substr($request, 0, 1) == '/') {
			$request = substr($request, 1, strlen($request)-1);
		}
		#We then explode the request by the slash. You can then calculate which page the user is requesting
		$request = explode('/', $request);
		#Removes any empty items. This is caused by double slashes in the URL
		foreach($request as $key => $value) {
			if($value == '') {
				array_splice($request, $key, 1);
			}
		}
		return $request;
	}
	
	public function getPage() {
		global $SITE;
		
		// add pages viewable by everyone, always here
		$this->availablePages = array('request', 'contact'); #, 'about', 'privacy', 'tos');
		
		if(SessionController::validSession() === true) {
			// add pages that only valid users can view here
			$this->availablePages = array_merge($this->availablePages, array('changepassword', 'settings', 'signout'));
			
			if(SessionController::getSessionType() === 'administrator') {
				// add pages that only the admin can view here
				$this->availablePages = array_merge($this->availablePages, array('admin'));
			}
		}
		else {
			// add pages that can only be viewed when there is no session here
			$this->availablePages = array_merge($this->availablePages, array('register', 'signin', 'forgotpassword', 'resetpassword'));
		}
		
		if(empty($this->url[0])) {
			$this->page = 'home';
		}
		else if(!in_array($this->url[0], $this->availablePages)) {
			exit(header("location: {$SITE['url']}"));
		}
		else {
			$this->page = $this->url[0];
		}
		
		return $this->page;
	}
	
	public function route() {
		global $SITE;
		
		if($this->page === 'request') {
			if(empty($this->url[1])) {
				exit(header("location: {$SITE['url']}"));
			}
			$reqc = new RequestController($this->url[1]);
		}
		else if($this->page === 'register') {
			$regc = new RegisterController();
			
			if($this->method === 'POST') {
				$params =
					array(
						'method' => $this->method,
						'name' => isset($_POST['name']) ? $_POST['name'] : '',
						'email' => isset($_POST['email']) ? $_POST['email'] : '',
						'p1' => isset($_POST['p1']) ? $_POST['p1'] : '',
						'p2' => isset($_POST['p2']) ? $_POST['p2'] : '',
						'ref' => isset($_POST['ref']) ? $_POST['ref'] : '',
					);
				$regc->setParams($params);
			}
			else if($this->method === 'GET' && !empty($this->url[1])) {
				$regc->setRef($this->url[1]);
			}
			return $regc->go();
		}
		else if($this->page === 'signin' && $this->method === 'POST') {
			$name = isset($_POST['name']) ? $_POST['name'] : '';
			$password = isset($_POST['password']) ? $_POST['password'] : '';
			$sc = new SessionController();
			return $sc->createSession($name, $password);
		}
		else if($this->page === 'signout') {
			$sc = new SessionController();
			return $sc->destroySession();
		}
		else if($this->page === 'forgotpassword' && $this->method === 'POST') {
			if(!isset($_POST['email'])) {
				exit(header("location: {$SITE['url']}forgotpassword"));
			}
			$fpc = new ForgotPasswordController();
			return $fpc->go($_POST['email']);
		}
		else if($this->page === 'resetpassword') {
			$rpc = new ResetPasswordController();
			
			if($this->method === 'POST') {
				$params =
					array(
						'method' => $this->method,
						'code' => isset($_POST['code']) ? $_POST['code'] : '',
						'p1' => isset($_POST['p1']) ? $_POST['p1'] : '',
						'p2' => isset($_POST['p2']) ? $_POST['p2'] : '',
					);
				$rpc->setParams($params);
			}
			else if($this->method === 'GET' && !empty($this->url[1])) {
				$rpc->setCode($this->url[1]);
			}
			return $rpc->go();
		}
		else if($this->page === 'changepassword' && $this->method === 'POST') {
			$rpc = new ResetPasswordController();
			
			$params =
				array(
					'method' => $this->method,
					'op' => isset($_POST['op']) ? $_POST['op'] : '',
					'p1' => isset($_POST['p1']) ? $_POST['p1'] : '',
					'p2' => isset($_POST['p2']) ? $_POST['p2'] : '',
				);
			$rpc->setParams($params);
			return $rpc->go();
		}
		else if($this->page === 'contact' && $this->method === 'POST') {
			$cc = new ContactController();
			
			$params =
				array(
					'name' => isset($_POST['name']) ? $_POST['name'] : '',
					'email' => isset($_POST['email']) ? $_POST['email'] : '',
					'subject' => isset($_POST['subject']) ? $_POST['subject'] : '',
					'message' => isset($_POST['message']) ? $_POST['message'] : '',
					'user_id' => SessionController::validSession() ? SessionController::getUserID() : 0,
				);
			$cc->setParams($params);
			return $cc->go();
		}
		
		return null;
	}
}

?>