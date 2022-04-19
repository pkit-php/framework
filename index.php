<?php

require __DIR__ . '/pkit/load.php';

use Pkit\Http\Router;
use Pkit\Http\Middleware\Queue;

Queue::setMap([
  "api" => API::class
]);

$router = new Router(__DIR__ . '/routes');
$router->init();
$router->run();
