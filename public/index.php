<?php
require '../vendor/autoload.php';
use Search\Search;

$loader = new Twig_Loader_Filesystem('../views/twig');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));



$search = new Search();

$website = $search->findHuman("Bruno Skvorc");

dump($website);
//dump($website);