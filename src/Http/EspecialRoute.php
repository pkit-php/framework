<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use ReflectionClass;
use Throwable;

class EspecialRoute
{
  public static function run(Request $request, Throwable $err)
  {
    $class = static::class;
    $route = new $class;

    if ($method = $route->getMethod($request)) {
      return $route->$method($request, $err);
    }

    return new Response($err->getMessage(), $err->getCode());
  }

  public function runMethod(Request $request, Throwable $err)
  {
    $all = 'all';
    if (
      method_exists($this, $all) &&
      (new ReflectionClass($this))
      ->getMethod($all)
      ->getDocComment() !== "/** @abstract */"
    ) {
      return $all;
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (
      in_array($method, $methods) &&
      method_exists($this, $method) &&
      (new ReflectionClass($this))
      ->getMethod($method)
      ->getDocComment() !== "/** @abstract */"
    ) {
      return $method;
    }
    return false;
  }
}
