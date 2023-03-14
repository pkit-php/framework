<?php

namespace Pkit\Throwable;

use Phutilities\Env;
use Pkit\Exceptions\Http\Status\InternalServerError;
use Pkit\Http\Response;
use Pkit\Http\Status;
use ReflectionObject;
use Throwable;

class Error extends \Exception
{
    public function __construct(string $message, int $code, Throwable|null $th = null)
    {
        if (
            $code < 400 ||
            $code >= 600 ||
            !Status::validate($code)
        ) {
            throw new InternalServerError(
                "Error: Status '$code' is not valid",
                $th
            );
        }
        parent::__construct($message, $code, $th);
    }

    public static function tryRun(\Closure $function)
    {
        try {
            ob_start();
            $function();
        }
        catch (Error $err) {
        }
        catch (Redirect $red) {
            exit(Response::code($red->getCode())
                ->header("Location", $red->getMessage()));
        }
        catch (Throwable $err) {
            if (is_int($err->getCode()))
                $code = Status::validate($err->getCode())
                    ? $err->getCode()
                    : 500;
            $codeProperty = (new ReflectionObject($err))
                ->getProperty("code");
            $codeProperty->setAccessible(true);
            $codeProperty->setValue($err, $code ?? 500);
        } finally {
            if (
                Env::getEnvOrValue("PKIT_CLEAR", "true") == "true" &&
                Env::getEnvOrValue("PKIT_RETURN", "true") == "true"
            )
                ob_end_clean();
            return $err ?? new InternalServerError(
                "Router: status and message is null or invalid",
            );
        }
    }
}