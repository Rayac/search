<?php

namespace Rayac;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class peopleController
{
    public function action (Request $request, Response $response, array $args)
    {

        dump($args);
        return $response;
    }
}