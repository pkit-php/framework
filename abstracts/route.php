<?php namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\Route as HttpRoute;
use Pkit\Utils\Methods;

abstract class Route extends HttpRoute
{
  public $middlewares = [];

  public function options(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function delete(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function patch(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function trace(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function post(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function head(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function get(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
  public function put(Request $request, Response $response)
  {
    Methods::methodNotAllowed($request, $response);
  }
}
