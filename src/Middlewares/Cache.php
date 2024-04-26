<?php

namespace Pkit\Middlewares;

use Pkit\Abstracts\Middleware;
use Pkit\Http\Request;
use Pkit\Utils\Cache as CacheUtil;

class Cache extends Middleware
{
    public function __invoke(Request $request, $next, $params)
    {
        $cache_params = [];
        $invalidate_routes = [];
        if (!is_null($params)) {
            $params = is_array($params) ? $params : [$params];
            $cache_params = @$params['cache_params'] ?? [];
            $invalidate_routes = @$params['invalidate'] ?? [];

            if (!empty($invalidate_routes)) {
                $return = $next($request);
                foreach ($invalidate_routes as $route) {
                    self::invalidateRoute($route);
                }
                return $return;
            }

            if ($expiration = @$params['expiration'])
                CacheUtil::config(CacheUtil::getCacheDir(), (int) $expiration);
        }

        $fileCache = self::formatFileCache(Request::getInstance()->uri, $cache_params, $request->queryParams);

        return CacheUtil::getCache($fileCache, fn() => $next($request));
    }

    private function formatFileCache(string $uri, array $cache_params, array $queryParams)
    {
        $paramsString = "?";
        foreach ($cache_params as $key) {
            if (is_null(@$queryParams[$key]))
                continue;
            $key = urlencode($key);
            $value = urlencode($queryParams[$key]);
            if (str_ends_with($paramsString, "?"))
                $paramsString .= "$key=$value";
            else
                $paramsString .= "&$key=$value";
        }
        return urlencode(ltrim($uri, "/")) . $paramsString . ".cache";
    }

    public function invalidateRoute(string $route)
    {
        if (!str_starts_with($route, "/"))
            $route = Request::getInstance()->uri . "/" . $route;
        $keys = null;
        $exploded = explode("?", $route,2);
        $route = $exploded[0];
        $keys = @$exploded[1];
        $trated_route = str_replace("%2A", "*", preg_quote(urlencode(ltrim($route, "/"))));
        $cache_file = CacheUtil::getCacheDir() . "/"
        . $trated_route
        . ($keys ? "?$keys" : "\?*")
        . ".cache";
        array_map('unlink', glob($cache_file));
    }

}