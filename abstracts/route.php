<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Route as HttpRoute;
use Pkit\Http\Status;

function methodNotAllowed($_, Response $response)
{
  $response
    ->onlyCode()
    ->setStatus(Status::METHOD_NOT_ALLOWED)
    ->send();
}

abstract class Route extends HttpRoute
{
  public $middlewares = [];

  public function options(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function delete(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function patch(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function trace(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function post(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function head(Request $request, Response $response)
  {
    $response->headers['Accept'] =
      'application/x-www-form-urlencoded, ' .
      'application/json, ' .
      'application/xml, ' .
      'multipart/form-data';
    $response->onlyCode();
    $response->send();
  }
  public function get(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
  public function put(Request $request, Response $response)
  {
    methodNotAllowed($request, $response);
  }
}
