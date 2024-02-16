<?php

use Pkit\Abstracts\Route;
use Pkit\Http\Request;
use Pkit\Http\Response;
use Pkit\Utils\Parser;

return new class extends Route {

    function GET(Request $request): Response
    {
        $postVars = $request->postVars;
        return new Response($postVars);
    }

    function POST(Request $request): Response
    {
        $contentType = Parser::headerToArray(@$request->headers["Content-Type"] ?? "", false);
        $postVars = $request->postVars;

        if (in_array('text/html', $contentType)) {
            return new Response($postVars);
        } else if (in_array('application/xml', $contentType)) {
            return Response::xml($postVars);
        } else if (
            in_array('application/json', $contentType) ?:
            in_array('*/*', $contentType)
        ) {
            return Response::json($postVars);
        } else {
            return new Response($postVars);
        }

    }

};