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

        //Find 5 more visits images
        //$plz = $app['db']->fetchAll("SELECT i.id, u.id, username, user_id,title,img_path,visits,private,created_at,likes, FROM images as i and users as u  WHERE u.id = user_id ORDER BY visits DESC LIMIT 5");
        $top5 = $app['db']->fetchAll("SELECT images.*, users.username FROM images, users WHERE user_id = users.id ORDER BY visits DESC LIMIT 5");
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
                'form'=> $form->createView()

        ]);
        $response->setContent($content);

        return $response;
    }

}