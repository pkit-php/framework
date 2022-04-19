<?php

require __DIR__ . '/pkit/index.php';

use Pkit\Http\Router;
use Pkit\Http\Middleware\Queue;

Queue::setMap([
  "api" => API::class
]);

$router = new Router(__DIR__ . '/routes');
$router->init();
$router->run();
