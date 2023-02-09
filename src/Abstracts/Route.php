<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Middlewares;
use Pkit\Http\Status;
use Pkit\Throwable\Error;
use ReflectionClass;
use ReflectionMethod;
use Throwable;

abstract class Route
{
    public $middlewares = [];
    public function getMethod(Request $request, bool $especialRoute = false)
    {
        $all = 'ALL';
        if (method_exists($this, $all)) {
            if ((new ReflectionClass($this))
                ->getMethod($all)
                ->getDocComment() !== "/** @abstract */"
            ) {
                return $all;
            }
        }

        $method = $request->httpMethod;
        if (in_array($method, ['GET', 'POST', 'PATCH', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'HEAD'])) {
            if (method_exists($this, $method)) 
                return $method;
            
            if ($especialRoute == false)
                throw new Error("Method Not Allowed", Status::METHOD_NOT_ALLOWED);
        }
        return false;
    }

  final public function __invoke(Request $request, ?Throwable $err = null)
  {
    if( is_null($err)){
      return $this->runRoute($request);
    }
    return $this->runEspecialRoute($request, $err);
  
  }

  public function runRoute(Request $request){
    if ($method = $this->getMethod($request)) {

      $middlewares = Middlewares::filterMiddlewares(
        $this->middlewares,
        $request->httpMethod
      );
      return (new Middlewares(function ($request) use ($method) {
        return $this->$method($request);
      },$middlewares))->next($request);
    }

    return new Response("", Status::NOT_IMPLEMENTED);
  }


  public function runEspecialRoute(Request $request, Throwable $err)
  {
    if ($method = $this->getMethod($request, true)) {
      return $this->$method($request, $err);
    }

    throw $err;
  }
 
}