<?php

namespace Pkit\Abstracts;

use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Http\EspecialRoute as HttpEspecialRoute;
use Throwable;

abstract class EspecialRoute extends HttpEspecialRoute
{
  /** @abstract */
  public function options(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function delete(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function patch(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function trace(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function post(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function head(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function get(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function put(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
  /** @abstract */
  public function all(Request $_, Throwable $err): Response
  {
    return new Response("");
  }
}
