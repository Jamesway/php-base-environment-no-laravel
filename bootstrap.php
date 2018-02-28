<?php

//disable displaying back errors
//ini_set('display_errors', 0);
error_reporting(E_ALL);
//error_reporting(E_ALL & ~ (E_WARNING | E_NOTICE));
date_default_timezone_set('America/Los_Angeles');


//composer autoload
require_once __DIR__ . '/vendor/autoload.php';


//DI Container - php-di
$builder = new \DI\ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAnnotations(false);
$builder->addDefinitions(__DIR__ . '/config.php');
$container = $builder->build();


//Router - FastRoute
//method and uri
$http_method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];


//check for a query string "?foo=bar..." and remove it
$pos = strpos($uri, "?");
if ($pos !== false) {

    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);


//make dispatcher
$dispatcher = $container->make('FastRoute\Dispatcher');

$route_data = $dispatcher->dispatch($http_method, $uri);


$params = [];
switch ($route_data[0]) {

    /* handling other routes */
    /* 405 Method Not Allowed
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:

        $allowed_methods = $route_data[1];
        echo "405";
        break;
    */

    case FastRoute\Dispatcher::FOUND:
    /* container build the controller class
       route_data[1] = controller class name
       route_data[2] = params
    */
        $controller_name = $route_data[1];
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

      //404
      //https://stackoverflow.com/a/5534333
      header("HTTP/1.0 404 Not Found");
      include "404.html";
      die();
}
