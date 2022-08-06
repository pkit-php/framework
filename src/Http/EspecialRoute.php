<?php

namespace Pkit\Http;

use Pkit\Http\Request;

class EspecialRoute
{
  public static function run(Request $request, string $message, int $code)
  {
    $class = static::class;
    $route = new $class;
    
    return $route->runMethod($request, $message, $code);
    
  }

  public function runMethod(Request $request, string $message, int $code)
  {
    $all = 'all';
    if (method_exists($this, $all)) {
      $return = $this->$all($request, $message, $code);
      if($return)
        return $return;
    }
    $method = strtolower($request->httpMethod);
    $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
    if (in_array($method, $methods) && method_exists($this, $method)) {
      return $this->$method($request, $message, $code);
    } else {
      return new Response("", Status::NOT_IMPLEMENTED);
    }
  }
}
