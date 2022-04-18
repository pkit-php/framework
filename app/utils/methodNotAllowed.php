<?php

function methodNotAllowed($_, Response $response)
{
  $response
    ->onlyCode()
    ->methodNotAllowed()
    ->send();
}
