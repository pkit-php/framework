<?php

require 'vendor/autoload.php';

use Pkit\Router;
use Pkit\DotEnv;

DotEnv::load(__DIR__ . "/.env");
DotEnv::load(__DIR__ . "/.env.local");

Router::run();