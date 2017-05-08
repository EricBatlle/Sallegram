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


//$app->get('/hello/{name}', 'SilexApp\\Controller\\HelloController::indexAction');
//$app->get('/add/{num1}/{num2}', 'SilexApp\\Controller\\HelloController::addAction');

$app->match('/', 'SilexApp\Controller\BaseController::redirectHome'); //Home
// SESSION
$before = function (Request $request, Application $app){
  if(!$app['session']->has('id')) {
      $response = new Response();
      $content = $app['twig']->render('error.twig', [
          'message' => 'You must be logged'
      ]);
      $response->setContent($content);
      $response->setStatusCode(Response::HTTP_FORBIDDEN);
      return $response;
  }
};
$app->get('/log', 'SilexApp\Controller\BaseController::logSession'); //Logeja, si ja ho estás, deslogeja
//$app->get('/log', 'SilexApp\Controller\BaseController::indexAction'); //Logeja, si ja ho estás, deslogeja
$app->get('/admin', 'SilexApp\Controller\BaseController::adminAction')->before($before); /*Nomes accessible per usuaris logejats */

// USER

$app->match('/users/get', 'SilexApp\Controller\UserController::editProfile')->before($before);
$app->match('/users/add', 'SilexApp\Controller\UserController::addUser');


$app->match('/users/login', 'SilexApp\Controller\UserController::loginUser');
$app->match('/addImg', 'SilexApp\Controller\UserController::addImg')->before($before);

$app->match('/users/validation/{id}', 'SilexApp\Controller\UserController::mailValidation');
//$app->match('/users/photos', 'SilexApp\Controller\UserController::mailValidation');
