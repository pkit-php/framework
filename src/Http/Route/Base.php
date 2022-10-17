<?php

namespace Pkit\Http\Route;

use Pkit\Http\Request;
use Pkit\Http\Status;
use Pkit\Throwable\Error;
use ReflectionClass;
use Throwable;

abstract class Base
{
    public function __invoke(Request $request, ?Throwable $err = null)
    {
        return $this->{"run"}($request, $err);
    }

    public function getMethod(Request $request, bool $especialRoute = false)
    {
        $all = 'all';
        if (method_exists($this, $all)) {
            if ((new \ReflectionClass($this))
                ->getMethod($all)
                ->getDocComment() !== "/** @abstract */"
            ) {
                return $all;
            }
        }
        $method = strtolower($request->httpMethod);
        $methods = ['get', 'post', 'patch', 'put', 'delete', 'options', 'trace', 'head'];
        if (in_array($method, $methods)) {
            if (
                method_exists($this, $method) &&
                (new ReflectionClass($this))
                ->getMethod($method)
                ->getDocComment() !== "/** @abstract */"
            ) {
                return $method;
            }
            if ($especialRoute == false)
                throw new Error("Method Not Allowed", Status::METHOD_NOT_ALLOWED);
        }
        return false;
    }
}
