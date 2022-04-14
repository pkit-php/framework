<?php

function methodNotAllowed($_, $response)
{
  $response
    ->onlyCode()
    ->methodNotAllowed()
    ->send();
}
