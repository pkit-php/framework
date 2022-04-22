<?php

namespace Pkit;

spl_autoload_register(function ($class) {
  $file = $_SERVER['DOCUMENT_ROOT'] . '/' . strtolower(str_replace('\\', '/', $class) . '.php');
  require_once $file;
});
