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
$app->get('/unlog', 'SilexApp\Controller\BaseController::unlogSession')->before($before); //deslogeja
$app->get('/log', 'SilexApp\Controller\BaseController::logSession'); //Logeja, si ja ho estás, deslogeja

// USER - Pages
$app->match('/users/get', 'SilexApp\Controller\UserController::editProfile')->before($before);
$app->match('/users/register', 'SilexApp\Controller\UserController::registerUser');
$app->match('/users/login', 'SilexApp\Controller\UserController::loginUser');
$app->match('/profile/{id}', 'SilexApp\Controller\UserController::publicProfile');
$app->match('/users/photos', 'SilexApp\Controller\UserController::userPhotos')->before($before);
$app->match('/addImg', 'SilexApp\Controller\UserController::addImg')->before($before);

$app->match('/users/validation/{id}', 'SilexApp\Controller\UserController::mailValidation');

//COMMENTS
$app->match('/addComment/{id}/{comment}', 'SilexApp\Controller\CommentController::addComment')->before($before);
$app->match('/comment/remove/{id}', 'SilexApp\Controller\CommentController::removeComment')->before($before);
$app->match('/comment/edit/{id}', 'SilexApp\Controller\CommentController::editComment')->before($before);
$app->match('/comment/add/{image_id}/{clicks}', 'SilexApp\Controller\CommentController::addMoreComments');
$app->match('/allComments', 'SilexApp\Controller\CommentController::allComments')->before($before);

//Photos/Images
$app->match('/myphotos/remove/{id}', 'SilexApp\Controller\PhotoController::removePhoto')->before($before);
$app->match('/myphotos/edit/{id}', 'SilexApp\Controller\PhotoController::editPhoto')->before($before);
$app->match('/photo/{id}', 'SilexApp\Controller\PhotoController::viewPhoto');
$app->match('/add/top5/{clicks}', 'SilexApp\Controller\PhotoController::addMoreTop5');
$app->match('/add/last5/{clicks}', 'SilexApp\Controller\PhotoController::addMoreLast5');

//Likes - Notificatinos
$app->match('/like/{status}/{id_image}', 'SilexApp\Controller\LikeController::like')->before($before);
$app->match('/notifications', 'SilexApp\Controller\LikeController::showNotifications')->before($before);
$app->match('/notification/remove/{id}', 'SilexApp\Controller\LikeController::removeNotification')->before($before);
