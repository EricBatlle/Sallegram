<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 19/04/2017
 * Time: 18:56
 */

namespace SilexApp\Controller;


use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Silex\Application;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints as Assert;


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
            'name' => 'Yourname',
            'email' => 'email@email.com'
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
                            'max' => 20,
                            'maxMessage' => 'El nombre es demsasiado largo'
                        )
                    ),
                )
            ))
            ->add('email', TextType::class, array(
                'constraints' => new Email(
                    array(
                        'message' => 'El formato del email no es válido'
                    ),
                    new NotBlank(
                        array(
                            'message' => 'El email no puede estar vacío'
                        )
                    )
                )
            ))
            ->add('birthdate', BirthdayType::class)
            ->add('password', PasswordType::class, array(
                'constraints' => new Regex(
                    array(
                        'pattern' => '/[a-z]/',
                        'match' => true,
                        'message' => 'La contraseña debe contener almenos una minuscula'
                    ),
                    array(
                        'pattern' => '/[A-Z]/',
                        'match' => true,
                        'message' => 'La contraseña debe contener almenos una mayuscula'
                    ),
                    array(
                        'pattern' => '/[0-9]/',
                        'match' => true,
                        'message' => 'La contraseña debe contener almenos un numero'
                    )
                )
            ))
            ->add('confirm_password', PasswordType::class)
            ->add('image_profile', FileType::class)
            ->add('submit',SubmitType::class, [
                'label' => 'Send',
            ])
		    ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            try{
                $app['db']->insert('user',[
                    'username' => $data['name'],
                    'email' => $data['email'],
                    'birthdate' => $data['birthdate']->format('Y-m-d'),
                    'password' => $data['password'],
                    'img_path' => $data['image_profile']
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

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('addUser.twig',array('form'=> $form->createView()));
        $response->setContent($content);

        return $response;
    }
}