<?php

namespace Pkit\Http;

use Pkit\Http\Request;
use Throwable;

class EspecialRoute
{
  public static function run(Request $request, Throwable $err)
  {
    $class = static::class;
    $route = new $class;
    
    return $route->runMethod($request, $err);
    
  }

  public function runMethod(Request $request, Throwable $err)
  {
    $all = 'all';
    if (method_exists($this, $all)) {
      $return = $this->$all($request, $err);
      if($return)
        return $return;
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (in_array($method, $methods) && method_exists($this, $method)) {
      return $this->$method($request, $err);
    } else {
      return new Response("", Status::NOT_IMPLEMENTED);
    }
  }
}
