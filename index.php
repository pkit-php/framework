<?php

include __DIR__ . '/pkit/http/router.php';
include __DIR__ . '/pkit/http/middleware/api.php';

Queue::setMap([
  "api" => API::class
]);

$router = new Router(__DIR__ . '/routes');
$router->init();
$router->run();
