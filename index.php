<?php

require __DIR__ . '/pkit/load.php';

use Pkit\Utils\DotEnv;
use Pkit\Database\Database;
use Pkit\Http\Router;
use Pkit\Http\Middleware\Queue;

(new DotEnv(__DIR__ . '/.env'))->load();

Queue::setMap([
  "api" => API::class
]);

Database::init([
  "driver" => getenv("DB"),
  "host" => getenv("DB_HOST"),
  "name" => getenv("DB_NAME"),
  "user" => getenv("DB_USER"),
  "pass" => getenv("DB_PASS"),
]);

$router = new Router(__DIR__ . '/routes');
$router->init();
$router->run();
