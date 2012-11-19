<?PHP

class RouteController {

  private
    $method,
    $request_uri,
    $query_string,
    $request;

  public function __construct() {
    $this->method = mb_strtolower($_SERVER['REQUEST_METHOD']);
    $this->parseURL();
  }

  private function parseURL() {
    $request_temp = explode('?', $_SERVER['REQUEST_URI']);
    $this->request_uri = $request_temp[0];
    $this->query_string = count($request_temp) > 1 ? $request_temp[1] : null;
    $this->request = explode('/', trim($this->request_uri, '\t\n /'));
  }

  public function route() {
    global $SETTINGS;

    if(in_array('api', (array)$this->request)) {

      $urlArray = array();
      $start = false;
      for ($i = 0; !empty($this->request[$i]); $i++) {
        if ($start) {
          $urlArray[] = $this->request[$i];
        }
        if ($this->request[$i] === 'api') {
          $start = true;
        }
      }

      $apic = new APIController();
      $apic->process($this->method, $urlArray);

    } else {
      require($SETTINGS['INDEX']);
    }
  }
}