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
    /*public function logSession(Application $app, User $user)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            return new Response('Session finished');
        }

        $app['session']->set('id',$user->id);
        $content = 'Session started for the user '.$app['session']->get('id');
        return new Response($content);
    }*/
    public function logSession(Application $app, $id)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            return new Response('Session finished');
        }

        $app['session']->set('id',$id);
        $content = 'Session started for the user '.$app['session']->get('id');
        return new Response($content);
    }

    public function unlogSession(Application $app)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            return new Response('Session finished');
        }

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

        //Find 5 more visits images
        //$plz = $app['db']->fetchAll("SELECT i.id, u.id, username, user_id,title,img_path,visits,private,created_at,likes, FROM images as i and users as u  WHERE u.id = user_id ORDER BY visits DESC LIMIT 5");
        $top5 = $app['db']->fetchAll("SELECT * FROM images ORDER BY visits DESC LIMIT 5");
        $last5 = $app['db']->fetchAll("SELECT * FROM images ORDER BY created_at DESC LIMIT 5");
var_dump($top5);
        $content = $app['twig']->render('home.twig',[
                'logged' => $app['session']->has('id'),
                'top5' => $top5,
                'last5' => $last5
        ]);
        $response->setContent($content);

        return $response;

    }
}