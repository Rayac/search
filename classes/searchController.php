<?php

namespace Rayac;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig_Environment;
use Twig_Loader_Filesystem;

class searchController
{

    public function action (Request $request, Response $response)
    {
        $loader = new Twig_Loader_Filesystem('../views/twig');
        $twig = new Twig_Environment($loader, array('cache' => false));
        $website = "";

        echo $twig->render("form.twig", ['websites' => $website]);
        return $response;
    }

    public function find (Request $request, Response $response, array $args)
    {
        $loader = new Twig_Loader_Filesystem('../views/twig');
        $twig = new Twig_Environment($loader, array('cache' => false));
        $pdo = Database::getPDO();
        $search = new Search($pdo);
        $website = $search->searchFor($_POST['find']);
        echo $twig->render("form.twig", ['websites' => $website]);
        return $response;
    }
}