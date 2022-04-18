<?php

function sanitizeURI(string $uri)
{
    $uri_base = explode('?', $uri)[0];
    $pure_uri = explode('#', $uri_base)[0];

    $xUri = $pure_uri ?? '/';
    $yUri = rtrim($xUri, '/');
    if ($yUri == '') {
        return '/';
    }

    return $yUri . '/';
}
