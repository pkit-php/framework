<?php

include __DIR__ . '/../utils/methodNotAllowed.php';

abstract class Route
{
  public function options($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function delete($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function patch($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function trace($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function post($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function head($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function get($request, $response)
  {
    methodNotAllowed($request, $response);
  }
  public function put($request, $response)
  {
    methodNotAllowed($request, $response);
  }
}
