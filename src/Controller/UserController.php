<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 19/04/2017
 * Time: 18:56
 */

namespace SilexApp\Controller;


use SilexApp\Controller\Validations\CorrectPassword;
use SilexApp\Controller\Validations\CorrectLogin;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Silex\Application;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
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
    public function getAction(Application $app, $id, Request $request)
    {
        $sql = "SELECT * FROM user WHERE id = ?";
        $user = $app['db']->fetchAssoc($sql, array((int)$id)); //llamando al servicio
        $response = new Response();

//        if (!$user) {
//            $response->setStatusCode(Response::HTTP_NOT_FOUND);
//            $content = $app['twig']->render('error.twig', [
//                    'message' => 'User not found'
//                ]
//            );
//        } else {
//            $response->setStatusCode(Response::HTTP_OK);
//            $content = $app['twig']->render('showUser.twig', [
//                    'user' => $user,
//                    'app' => [
//                        'username' => $app['app.name']
//                    ]
//                ]
//            );
//        }
//        $response->setContent($content);
//        return $response;

        $data = array(
            'name' => $user['username'],
            //'image_profile' => $user['img_path']
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
                    new Regex(
                        array(
                            'pattern' => '/^[a-zA-Z0-9]+$/',
                            'match' => true,
                            'message' => 'El nombre debe contener solo caracteres alfanumericos'
                        ))
                )
            ))

            ->add('birthdate', BirthdayType::class, array(
                'input' => 'string',
                'data' => $user['birthdate'],
                'constraints' => new Assert\Range(
                    array(
                        'max' => 'now',
                        'maxMessage' => 'La fecha ha de ser anterior a la actual'
                    )
                )
            ))

            ->add('password', RepeatedType::class, array(
                'constraints' => array(
                    new Length(
                        array(
                            'min' => 6,
                            'max' => 12,
                            'minMessage' => 'La contraseña debe contener entre 6 y 12 caracteres',
                            'maxMessage' => 'La contraseña debe contener entre 6 y 12 caracteres'
                        )
                    ),
                    new Regex(
                        array(
                            'pattern' => '/[a-z]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos una minuscula'
                        )),
                    new Regex(
                        array(
                            'pattern' => '/[A-Z]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos una mayuscula'
                        )),
                    new Regex(
                        array(
                            'pattern' => '/[0-9]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos un numero'
                        ))
                ),
                'type' => PasswordType::class,
                'invalid_message' => 'Les contrasenyes han de coincidir',
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))

            ->add('image_profile', FileType::class, array(
                'required' => false

            ))

            ->add('submit',SubmitType::class, [
                'label' => 'Save',
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
                        'password' => md5($data['password']),
                        'img_path' => $data['image_profile']
                    ]
                );
                $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
                $id = $lastInsertedId['id'];
                $url = '/users/get/'.$id;
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
        $content = $app['twig']->render('showUser.twig',array('form'=> $form->createView()));
        $response->setContent($content);

        return $response;


    }

    /**
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function postAction(Application $app, Request $request)
    {
        $response = new Response();

        $data = array(
            'name' => 'Yourname',
            'email' => 'email@email.com'
        );
        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('name', TextType::class, array(
                'constraints' => array(
                    new Length(
                        array(
                            'max' => 20,
                            'maxMessage' => 'El nombre es demsasiado largo'
                        )
                    ),
                    new Regex(
                        array(
                            'pattern' => '/^[a-zA-Z0-9]+$/',
                            'match' => true,
                            'message' => 'El nombre debe contener solo caracteres alfanumericos'
                    ))
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

            ->add('birthdate', BirthdayType::class, array(
                'constraints' => new Assert\Range(
                    array(
                        'max' => 'now',
                        'maxMessage' => 'La fecha ha de ser anterior a la actual'
                    )
                )
            ))
            ->add('password', RepeatedType::class, array(
                'constraints' => array(
                    new Length(
                        array(
                            'min' => 6,
                            'max' => 12,
                            'minMessage' => 'La contraseña debe contener entre 6 y 12 caracteres',
                            'maxMessage' => 'La contraseña debe contener entre 6 y 12 caracteres'
                        )
                    ),
                    new Regex(
                        array(
                            'pattern' => '/[a-z]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos una minuscula'
                    )),
                    new Regex(
                        array(
                            'pattern' => '/[A-Z]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos una mayuscula'
                    )),
                    new Regex(
                        array(
                            'pattern' => '/[0-9]/',
                            'match' => true,
                            'message' => 'La contraseña debe contener almenos un numero'
                    ))
                ),
                'type' => PasswordType::class,
                'invalid_message' => 'Les contrasenyes han de coincidir',
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('image_profile', FileType::class, array(
                'required' => false
            ))
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
                    'password' => md5($data['password']),
                    'img_path' => $data['image_profile']
                ]
            );
            $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM user ORDER BY id DESC LIMIT 1');
            $id = $lastInsertedId['id'];
            $url = '/users/get/'.$id;
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


    public function loginUser(Application $app, Request $request)
    {
        $response = new Response();
        $match = true;
        $data = array(
            'username-email' => 'Yourname',
        );

        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('username-email', TextType::class, array(
                'constraints' => array(
                    'constraints' => new CorrectLogin(
                        array(
                            'message' => 'Invalid Login: Must contain alphanumeric values, and less than 20 characters (not HTML syntax)'
                        )
                    )
                )
            ))
            ->add('password', PasswordType::class, array(
                'constraints' => new CorrectPassword(
                    array(
                        'message' => 'Invalid Password: Must contain one minus, one mayus, one number, and 6 to 12 characters (not HTML syntax)'
                    )
                )
            ))
            ->add('submit',SubmitType::class, [
                'label' => 'Send',
            ])
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){

            $data = $form->getData();
            try{
                $login = $data['username-email'];
                $pass = $data['password'];

                $match = $app['db']->fetchAssoc("SELECT * FROM user WHERE (username = '$login' OR email = '$login')  AND password = '$pass'");
                if($match == true){
                    $url = '/users/home';
                    return new RedirectResponse($url);
                }else{
                    $response->setStatusCode(Response::HTTP_OK);
                    $content = $app['twig']->render('loginUser.twig',array(
                        'form'=> $form->createView(),
                        'logged' => $match
                    ));
                    $response->setContent($content);

                    return $response;
                }/*else{
                    //ToDo: Home
                    $url = '/users/home';
                }
                return new RedirectResponse($url);*/

            }catch(Exception $e){
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('error.twig',[
                    'errors' => [
                        'unexpected' => 'An error has ocurred, please try it again later'
                    ]
                ]);
                $response->setContent($content);
                return $response;
            }
        }

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('loginUser.twig',array(
            'form'=> $form->createView(),
            'logged' => $match
        ));
        $response->setContent($content);

        return $response;
    }
}