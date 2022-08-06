<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Route as HttpRoute;
use Pkit\Http\Status;

function methodNotAllowed()
{
  return new Response("Method Not Allowed", Status::METHOD_NOT_ALLOWED);
}

abstract class Route extends HttpRoute
{
  public $middlewares = [];

  public function options(Request $_)
  {
    methodNotAllowed();
  }
  public function delete(Request $_)
  {
    methodNotAllowed();
  }
  public function patch(Request $_)
  {
    methodNotAllowed();
  }
  public function trace(Request $_)
  {
    methodNotAllowed();
  }
  public function post(Request $_)
  {
    methodNotAllowed();
  }
  public function head(Request $_)
  {
    echo (new Response(""))
      ->header('Accept',
    'application/x-www-form-urlencoded, ' .
    'application/json, ' .
    'application/xml, ' .
    'multipart/form-data');
      exit;
  }
  public function get(Request $_)
  {
    methodNotAllowed();
  }
  public function put(Request $_)
  {
    methodNotAllowed();
  }
  public function all(Request $_)
  {
  }
}
