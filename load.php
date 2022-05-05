<?php

namespace Pkit;

spl_autoload_register(function ($class) {
  $baseFile = str_replace('\\', '/', $class) . '.php';
  if (substr($class, 0, 4) === "Pkit") {
    $file = __DIR__ . '/../' . $baseFile;
  } else {
    $file = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseFile;
  }
  require_once $file;
});
