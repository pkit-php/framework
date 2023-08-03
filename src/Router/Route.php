<?php

namespace Pkit\Router;

use Phutilities\Env;
use Phutilities\Text;
use Pkit\Abstracts\MatchParam;
use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Router;
use Throwable;

class match_integer extends MatchParam
{
    public string $regex = '[0-9]+';
    public ?string $restRegex = null;

    public function convert(string $test)
    {
        return (int) $test;
    }

}

class match_int extends match_integer
{
}

class match_float extends MatchParam
{
    public string $regex = '[0-9]+(?:\.[0-9])?';
    public ?string $restRegex = null;

    public function convert(string $test)
    {
        return (float) $test;
    }

}

class match_word extends MatchParam
{
    public string $regex = '\w+';
    public ?string $restRegex = '[\w\/]+';

}

class match_file extends MatchParam
{
    function convert($test)
    {
        return pathinfo($test);
    }
}

class Route
{

    private static
    $regexVariable = '/\[(\.\.\.)?(\w+)(?:\:(\w+))?\]/',
    $notRegexVariable = '/^(\[(?:\.\.\.)?(\w+)(?:\:\w+)?\])/';
    private string $routeFile;
    readonly public array $variables;

    function __construct(string $routeFile, array $variables)
    {
        $this->routeFile = $routeFile;
        $this->variables = $variables;
    }

    public function run(Request $request, ?Throwable $err = null): string
    {
        $routeInfo = pathinfo($this->routeFile);

        if ($routeInfo["extension"] !== "php")
            return Response::mimeFile(Router::getPublicPath() . $this->routeFile);

        $return = include Router::getRoutePath() . $this->routeFile;
        if (is_string($return))
            return $return;

        if (is_callable($return) == false && Env::getEnvOrValue("PKIT_RETURN", "true") == "true")
            throw new InternalServerError("The route $this->routeFile was not a valid return");

        if ($return === 1 || is_null($return))
            return "";

        return $return($request, $err);
    }

    public static function matchRoute(string $routeFile, string $uri)
    {
        $route = str_ends_with($routeFile, "/index.php")
            ? Text::removeFromEnd($routeFile, "index.php")
            : Text::removeFromEnd($routeFile, ".php");
        $route = str_replace('/', '\/', $route);

        $variablesConverts = [];

        $regexRoute = self::replaceRouteToRegex($route, $variablesConverts);

        if (preg_match($regexRoute, $uri, $matches)) {

            unset($matches[0]);
            foreach ($matches as $key => $value) {
                if (is_int($key)) {
                    unset($matches[$key]);
                }
                else {
                    $converter = $variablesConverts[$key];
                    if ($converter->validate($value) == false) {
                        return false;
                    }
                    $matches[$key] = $converter->convert($value);

                }
            }

            return new self($routeFile, $matches);
        }

        return false;
    }

    private static function replaceRouteToRegex($route, &$variablesConverts)
    {
        $regexRoute = preg_replace_callback(self::$regexVariable, function ($match) use (&$variablesConverts) {
            $isRest = !!@$match[1];
            $varName = @$match[2];
            $type = @$match[3];

            $matchTest = is_string($type) ? self::matchByType($type) : new MatchParam();

            if ($isRest) {
                if ($temp = $matchTest->restRegex)
                    $regex = $temp;
                else
                    throw new InternalServerError("type $type not suporte rest param");
            }
            else {
                $regex = $matchTest->regex;
            }
            $variablesConverts[$varName] = $matchTest;

            return "(?<" . $varName . ">" . $regex . ")";
        }, $route);

        $regexRoute = preg_replace_callback(self::$notRegexVariable, function ($match) {
            return preg_quote($match[0]);
        }, $regexRoute);
        return "/^$regexRoute$/";
    }

    private static function matchByType($type)
    {
        if (class_exists(__NAMESPACE__ . "\\match_$type")) {
            $matchClass = __NAMESPACE__ . "\\match_$type";
            return new $matchClass;
        }
        elseif (class_exists("Pkit\\$type\\match")) {
            $matchClass = "Pkit\\$type\\match";
            return new $matchClass;
        }
        elseif (class_exists("App\\Matches\\match_$type")) {
            $matchClass = "App\\Matches\\match_$type";
            return new $matchClass;
        }
        throw new InternalServerError("type $type to match, invalid");
    }


}