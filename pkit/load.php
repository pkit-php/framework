<?php namespace Pkit;

spl_autoload_register(function ($class) {
  require_once __DIR__ . '/../' . strtolower(str_replace('\\', '/', $class) . '.php');
});
