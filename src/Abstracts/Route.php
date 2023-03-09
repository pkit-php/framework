<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Status;
use Pkit\Middlewares;
use Pkit\Throwable\Error;
use ReflectionMethod;
use Throwable;

abstract class Route
{
  public $middlewares = [];

  protected final function getMethod(Request $request, bool $especialRoute = false): string|false
  {
    $all = 'ALL';
    if (method_exists($this, $all)) {
      return $all;
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

  public final function __invoke(Request $request, ?Throwable $err = null): Response
  {
    if (is_null($err)) {
      return $this->runRoute($request);
    }
    return $this->runEspecialRoute($request, $err);
  }

  protected final function runRoute(Request $request): Response
  {
    if ($method = $this->getMethod($request)) {
      $attributedMiddlewares = @(new ReflectionMethod($this, $method))->getAttributes(Middlewares::class)[0];

      $middlewares = Middlewares::filterMiddlewares(
        $this->middlewares,
        $request->httpMethod
      );
      return (new Middlewares($middlewares))->setController(function ($request) use ($method, $attributedMiddlewares) {
        if ($attributedMiddlewares) {
          return $attributedMiddlewares
            ->newInstance()
            ->setController(
              function ($request) use ($method) {
                return $this->$method($request);
              }
            )->next($request);
        }

        return $this->$method($request);
      })->next($request);
    }

    return new Response("", Status::NOT_IMPLEMENTED);
  }


  protected final function runEspecialRoute(Request $request, Throwable $err): Response
  {
    if ($method = $this->getMethod($request, true)) {
      return $this->$method($request, $err);
    }

    throw $err;
  }

}