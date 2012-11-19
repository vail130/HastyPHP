<?PHP

class RouteController {

  private
    $url,
    $method;

  public function __construct() {
    $this->method = mb_strtolower($_SERVER['REQUEST_METHOD']);
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

  public function route() {
    global $SITE;

    if(in_array('api', (array)$this->url)) {

      $urlArray = array();
      $start = false;
      for ($i = 0; !empty($this->url[$i]); $i++) {
        if ($start) {
          $urlArray[] = $this->url[$i];
        }
        if ($this->url[$i] === 'api') {
          $start = true;
        }
      }

      $apic = new APIController();
      $apic->process($this->method, $urlArray);

    } else {
      require($SITE['index']);
    }
  }
}