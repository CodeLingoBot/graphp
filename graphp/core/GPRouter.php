<?

class GPRouter extends GPObject {

  use
    GPSingletonTrait;

  private
    $routes,
    $parts;

  public function __construct() {
    parent::__construct();
    $this->routes = require_once '../config/routes.php';
    $this->process();
    // TODO (mikeland86): allow override of default routing
    $this->defaultRouting();
  }

  private function process() {
    $uri = $_SERVER['REQUEST_URI'];
    if (isset($this->routes[$uri])) {
      // TODO (mikeland86): Add regex support
      $this->parts = $this->routes[$uri];
    } else {
      $this->parts = array_values(array_filter(explode('/', $uri)));
    }
  }

  private function defaultRouting() {
    $controller_name = ucfirst(idxx($this->parts, 0));
    $method_name = idxx($this->parts, 1);
    GPLoader::sharedInstance()->loadController($controller_name);
    $controller = new $controller_name();
    $args = array_slice($this->parts, 2);
    call_user_func_array([$controller, $method_name], $args);
  }
}