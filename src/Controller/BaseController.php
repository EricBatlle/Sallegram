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
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    public function logSession(Application $app, $id,$username,$img)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            $app['session']->remove('name');
            $app['session']->remove('img');

            return new Response('Session finished');
        }

        $app['session']->set('id',$id);
        $app['session']->set('name',$username);
        $app['session']->set('img',$img);
        $content = 'Session started for the user '.$app['session']->get('id');
        return new Response($content);
    }

    public function unlogSession(Application $app)
    {
        if($app['session']->has('id')){
            $app['session']->remove('id');
            }

        $url = '/';
        return new RedirectResponse($url);
    }

    public function redirectHome(Application $app){
        //var_dump($app['user']);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        $id = $app['session']->get('id');

        //Find 5 more visits images
        $top5 = $app['db']->fetchAll("SELECT images.*, users.username FROM images, users WHERE user_id = users.id ORDER BY visits DESC LIMIT 5");
        if($id != null){
            $liked = $app['db']->fetchAll("SELECT images.id FROM (likes LEFT JOIN users ON users.id = likes.user_id) LEFT JOIN images ON likes.image_id = images.id WHERE likes.user_id = $id");
        }else{
            $liked = false;
        }
        //Find 5 last creeated images
        $last5 = $app['db']->fetchAll("SELECT images.*, users.username  FROM images, users WHERE user_id = users.id ORDER BY created_at DESC LIMIT 5");

        $data = array(
            'Comment' =>  'Say something nice...',
        );

        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('Comment', TextareaType::class, array(

            ))
            ->add('submit',SubmitType::class, [
                'label' => 'Send',
            ])
            ->getForm();

        //$form->handleRequest($request);

        if($form->isValid()){

        }

        $content = $app['twig']->render('home.twig',[
                'top5' => $top5,
                'last5' => $last5,
                'liked' => $liked,
                'form'=> $form->createView()

        ]);
        $response->setContent($content);

        return $response;
    }

}