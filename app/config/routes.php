<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 03/04/2017
 * Time: 15:44
 */
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


$app->get('/hello/{name}', 'SilexApp\\Controller\\HelloController::indexAction');
$app->get('/add/{num1}/{num2}', 'SilexApp\\Controller\\HelloController::addAction');
$app->get('/users/get/{id}', 'SilexApp\Controller\UserController::getAction');
$app->match('/users/add', 'SilexApp\Controller\UserController::postAction');
// SESSION
$before = function (Request $request, Application $app){
  if(!$app['session']->has('name')) {
      $response = new Response();
      $content = $app['twig']->render('error.twig', [
          'message' => 'You must be logged'
      ]);
      $response->setContent($content);
      $response->setStatusCode(Response::HTTP_FORBIDDEN);
      return $response;
  }
};
$app->get('/', 'SilexApp\Controller\BaseController::indexAction');
$app->get('/admin', 'SilexApp\Controller\BaseController::adminAction')->before($before); /*Nomes accessible per usuaris logejats */
// USER

$app->get('/users/get/{id}', 'SilexApp\Controller\UserController::getAction');
$app->get('/users/add', 'SilexApp\Controller\UserController::postAction');


$app->match('/users/login', 'SilexApp\Controller\UserController::loginUser');
