<?php

namespace Rayac;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;

class peopleController
{
    public function action (Request $request, Response $response, array $args)
    {
        $loader = new Twig_Loader_Filesystem('../views/twig');
        $twig = new Twig_Environment($loader, array('cache' => false));

        $profile = new fullContact($args['link']);;

        echo $twig->render("profile.twig", ['profile' => $profile->results()]);
        return $response;
    }


}