<?php



ini_set('display_errors', 0); //prevent log dups
error_reporting(E_ALL);
date_default_timezone_set('America/Los_Angeles');
//error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE));
ini_set('display_errors', 0); //prevent log dups

require_once __DIR__ . '/vendor/autoload.php';


//php-di container
$builder = new DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$builder->addDefinitions(__DIR__ . "/config.php");
$container = $builder->build();


//COMMANDLINE
/*if (php_sapi_name() === "cli") {

    try {

      if (!isset($argv[1])) {

        throw new InvalidArgumentException("missing argument", 1);
      }

      $cli_controller_name = $container->get('cli.ns') . '\\' . $argv[1] . "CliController";
      $cli_controller = $container->make($cli_controller_name);
      $cli_controller->execute($argv);

    } catch (Exception $e) {

      echo "oops: " . $e->getMessage();
    }

    exit;
}*/



//method and uri
$http_method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

//check for a query string "?foo=bar..." and decode it
if (strpos($uri, "?") !== false) {

    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);


//make dispatcher
$dispatcher = $container->make('FastRoute\Dispatcher');


$route_data = $dispatcher->dispatch($http_method, $uri);

$params = [];
switch ($route_data[0]) {

  case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
    $allowed_methods = $route_data[1];
    // ... 405 Method Not Allowed
    echo "405";
    break;

  case FastRoute\Dispatcher::FOUND:

    //build the controller class
    $controller_name = $container->get('web.ns') . '\\' . $route_data[1];
    echo $controller_name;
    $params = $route_data[2];

    try {

      $controller = $container->make($controller_name);
      $controller->execute($params);

    } catch (Exception $e) {

      echo $e->getMessage();
    }
    break;

  case FastRoute\Dispatcher::NOT_FOUND:
  default:

    // ... 404 Not Found
    header("Location: " . $_SERVER['HTTP_HOST'] . "/404"); /* Redirect browser */
    exit();
    /*echo "404";
    break;*/
}
