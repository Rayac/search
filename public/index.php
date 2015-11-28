<?php
require '../vendor/autoload.php';
use Search\Search;

$loader = new Twig_Loader_Filesystem('../views/twig');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));
$search = new Search();
$website = "";






if (isset($_POST['find'])) {
    $website = $search->findHuman($_POST['find']);
}


echo $twig->render("form.twig");
echo $website;
