<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 19/04/2017
 * Time: 18:56
 */

namespace SilexApp\Controller;


use Doctrine\DBAL\Types\TextType;
use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class UserController
{
    public function getAction(Application $app, $id)
    {
        $sql = "SELECT * FROM user WHERE id = ?";
        $user = $app['db']->fetchAssoc($sql, array((int)$id)); //llamando al servicio
        $response = new Response();
        if (!$user) {
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $content = $app['twig']->render('error.twig', [
                    'message' => 'User not found'
                ]
            );
        } else {
            $response->setStatusCode(Response::HTTP_OK);
            $content = $app['twig']->render('showUser.twig', [
                    'user' => $user,
                    'app' => [
                        'name' => $app['app.name']
                    ]
                ]
            );
        }
        $response->setContent($content);
        return $response;
    }

    public function postAction(Application $app, Request $request)
    {
        $response = new Response();

        $data = array(
            'name' => 'Your name',
            'email' => 'Your email',
        );

        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('name', TextType::class, array(
                'constraints' => array(
                    new NotBlank(
                        array(
                            'message' => 'El nombre no puede estar vacío'
                        )
                    ),
                    new Length(
                        array(
                            'min' => 5,
                            'minMessage' => 'El nombre es demsasiado corto'
                        )
                    )
                )
            ))
            ->add('email', TextType::class, array(
                'constraints' => new Email(
                    array(
                        'message' => 'El formato del email no es válido'
                    )
                )
            ))
            ->add('submit',SubmitType::class, [
                'label' => 'Add user',
            ])
		    ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            try{
                $app['db']->insert('user',[
                    'name' => $data['name'],
                    'email' => $data['email']
                ]
            );
            $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
            $id = $lastInsertedId['id'];
            $url = '/users/get'.$id;
            return new RedirectResponse($url);
            }catch(Exception $e){
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('addUser.twig',[
                    'errors' => [
                        'unexpected' => 'An error has ocurred, please try it again later'
                    ]
                ]);
                $response->setContent($content);
                return $response;
            }
        }
        /*
        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('addUser.twig',array('form'=> $form->createView()));
        $response->setContent($content);

        return $response;*/
    }
}