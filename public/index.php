<?php
require '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$router = new League\Route\RouteCollection;

$router->addRoute('GET', '/', 'Rayac\searchController::action');
$router->addRoute('POST', '/', 'Rayac\searchController::find');
$router->addRoute('GET', '/{link}', 'Rayac\peopleController::action');

$dispatcher = $router->getDispatcher();
$request = Request::createFromGlobals();
$response = $dispatcher->dispatch($request->getMethod(), $request->getPathInfo());
$response->send();
