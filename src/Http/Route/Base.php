<?php

namespace Pkit\Http\Route;

use Pkit\Http\Request;
use ReflectionClass;

class Base
{
    public function getMethod(Request $request)
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
        if (
            in_array($method, $methods) &&
            method_exists($this, $method) &&
            (new ReflectionClass($this))
            ->getMethod($method)
            ->getDocComment() !== "/** @abstract */"
        ) {
            return $method;
        }
        return false;
    }
}
