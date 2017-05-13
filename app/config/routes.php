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
$app->get('/unlog', 'SilexApp\Controller\BaseController::unlogSession'); //deslogeja
$app->get('/log', 'SilexApp\Controller\BaseController::logSession'); //Logeja, si ja ho estás, deslogeja

// USER - Pages
$app->match('/users/get', 'SilexApp\Controller\UserController::editProfile')->before($before);
$app->match('/users/register', 'SilexApp\Controller\UserController::registerUser');
$app->match('/users/login', 'SilexApp\Controller\UserController::loginUser');
$app->match('/profile/{id}', 'SilexApp\Controller\UserController::publicProfile');
$app->match('/users/photos', 'SilexApp\Controller\UserController::userPhotos')->before($before);
$app->match('/allComments', 'SilexApp\Controller\UserController::allComments')->before($before);
$app->match('/addImg', 'SilexApp\Controller\UserController::addImg')->before($before);

$app->match('/users/validation/{id}', 'SilexApp\Controller\UserController::mailValidation');


//COMMENTS
$app->match('/addComment/{id}/{comment}', 'SilexApp\Controller\CommentController::addComment')->before($before);
//ToDo: change match to post
$app->match('/comment/remove/{id}', 'SilexApp\Controller\CommentController::removeComment')->before($before);
$app->match('/comment/edit/{id}', 'SilexApp\Controller\CommentController::editComment')->before($before);
//Photos
$app->match('/myphotos/remove/{id}', 'SilexApp\Controller\PhotoController::removePhoto')->before($before);
$app->match('/myphotos/edit/{id}', 'SilexApp\Controller\PhotoController::editPhoto')->before($before);

