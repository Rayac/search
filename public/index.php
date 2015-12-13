<?php
require '../vendor/autoload.php';
use Rayac\Database;
use Rayac\Search;

$loader = new Twig_Loader_Filesystem('../views/twig');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));
$pdo = Database::getPDO();
$search = new Search($pdo);
$website = "";


if (isset($_POST['find'])) {
    $website = $search->searchFor($_POST['find']);
}


echo $twig->render("form.twig", ['websites' => $website]);