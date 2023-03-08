<?php

require 'vendor/autoload.php';

use Pkit\Router;
use Phutilities\Env;

Env::load(__DIR__ . "/.env");
Env::load(__DIR__ . "/.env.local");

Router::run();