
<?php

const BASE_PATH = __DIR__ . '/../';

session_start();

require_once BASE_PATH . 'Core/functions.php';
require_once BASE_PATH . 'vendor/autoload.php';

spl_autoload_register(function (string $class): void {

  $namespaces = ['Core\\', 'Models\\', 'Services\\'];

  $esClasePropia = false;

  foreach ($namespaces as $namespace) {
    if (str_starts_with($class, $namespace)) {
      $esClasePropia = true;
      break;
    }
  }

  if (!$esClasePropia) {
    return;
  }

  $archivo = base_path(
    str_replace('\\', '/', $class) . '.php'
  );

  if (is_file($archivo)) {
    require_once $archivo;
  }
});

require base_path('bootstrap.php');

$router = new \Core\Router();
require base_path('routes.php');

$uri = parse_url(
  $_SERVER['REQUEST_URI'],
  PHP_URL_PATH
);

$baseUri = rtrim(
  str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])),
  '/'
);

if (
  $baseUri !== '' &&
  str_starts_with($uri, $baseUri . '/')
) {
  $uri = substr($uri, strlen($baseUri));
} elseif ($uri === $baseUri) {
  $uri = '/';
}

if ($uri === '/index.php') {
  $uri = '/';
}

$uri = $uri ?: '/';

$method = $_POST['_method']
  ?? $_SERVER['REQUEST_METHOD'];

$router->route($uri, $method);
