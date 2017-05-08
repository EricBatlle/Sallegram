<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 27/04/2017
 * Time: 16:30
 */

namespace SilexApp\Controller;

use Silex\Application;
use SilexApp\Model\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    public function logSession(Application $app, User $user)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            return new Response('Session finished');
        }
      
        $app['session']->set('id',$user->id);
        $content = 'Session started for the user '.$app['session']->get('id');
        return new Response($content);
    }

    /*public function adminAction(Application $app)
    {
        $content = "Welcome back ".$app['session']->get('name');
        return new Response($content);
    }*/

    public function redirectHome(Application $app){
        //var_dump($app['user']);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('home.twig',[
                'logged' => $app['session']->has('id')
        ]);
        $response->setContent($content);

        return $response;

    }
}