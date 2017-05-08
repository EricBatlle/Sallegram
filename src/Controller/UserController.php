<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 19/04/2017
 * Time: 18:56
 */

namespace SilexApp\Controller;

use SilexApp\Model\Entity\User;
use SilexApp\Model\Entity\UserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use SilexApp\Controller\Validations\CorrectPassword;
use SilexApp\Controller\Validations\CorrectLogin;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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



class UserController extends BaseController
{
    public function userPhotos (Application $app){

    }

    public function mailValidation(Application $app){
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        $host = $_SERVER["REQUEST_URI"];
        $id = explode ("/", $host);

        $sql = "SELECT * FROM users WHERE id = ?";
        $user = $app['db']->fetchAssoc($sql, array((int)$id[3])); //llamando al servicio
        if ($user != false){
            try {
                $app['db']->update('users', [
                    'active' => '1'],
                    array('id' => $user['id']));

                $this->logSession($app,$user['id']);
                $url = '/';
                return new RedirectResponse($url);

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
        } else {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('error.twig',[
                'errors' => [
                    'unexpected' => 'An error has ocurred, please try it again later'
                ]
            ]);

            $response->setContent($content);
            return $response;
        }

        return $response;
    }

    public function editProfile (Application $app, Request $request)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $id = $app['session']->get('id');
        var_dump($app['session']->get('id'));
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
        );

        /** @var Form $form */
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
                'input' => 'datetime',
                'data' => new \DateTime($user['birthdate']),
                'constraints' => new Assert\Range(
                    array(
                        'max' => 'now',
                        'maxMessage' => 'La fecha ha de ser anterior a la actual'
                    )
                )
            ))

            ->add('password', RepeatedType::class, array(
                'constraints' => new CorrectPassword(
                    array(
                        'message' => 'Invalid Password: Must contain one minus, one mayus, one number, and 6 to 12 characters (not HTML syntax)'
                    )
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

                $app['db']->update('users',[
                        'username' => $data['name'],
                    //ToDo: Check if string is needed
                    'birthdate' => (string)$data['birthdate']->format('Y-m-d'),
                        'password' => md5($data['password']),
                        'img_path' => $data['image_profile']],
                        array('id' => $app['session']->get('id')));

                $url = '/users/get';
                return new RedirectResponse($url);
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
        $content = $app['twig']->render('showUser.twig',array('form'=> $form->createView()));
        $response->setContent($content);

        return $response;
    }

    /**
     * @param Application $app
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addUser (Application $app, Request $request)
    {
        $response = new Response();

        $data = array(
            'name' => 'Yourname',
            'email' => 'email@email.com'
        );
        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('name', TextType::class, array(
                'constraints' => new CorrectLogin(
                    array(
                        'message' => 'Invalid Name: Must contain one minus, one mayus, one number, and 6 to 12 characters (not HTML syntax)'
                    )
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
                'constraints' => new CorrectPassword(
                    array(
                        'message' => 'Invalid Password: Must contain one minus, one mayus, one number, and 6 to 12 characters (not HTML syntax)'
                    )
                ),
                'type' => PasswordType::class,
                'invalid_message' => 'Les contrasenyes han de coincidir',
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
            ->add('image_profile', FileType::class, array(
                //ToDo: Required false?
                'required' => false
            ))
            ->add('submit',SubmitType::class, [
                'label' => 'Send',
            ])
		    ->getForm();

        $form->handleRequest($request);

        if($form->isValid()){
            $data = $form->getData();
            //IMAGE
            $dir = 'assets/uploads';
            //ToDo: If image=null -> take default image
            /** @var UploadedFile $someNewFilename */
            $someNewFilename = $data['image_profile'];
            $someNewFilename->move($dir, 'test.jpg');
            //$myFile = $request->files->get('image_profile');
            //$myFile->move($dir,$someNewFilename);
            try{
                $app['db']->insert('users',[
                    'username' => $data['name'],
                    'email' => $data['email'],
                    'birthdate' => $data['birthdate']->format('Y-m-d'),
                    'password' => md5($data['password']),
                    'img_path' => $data['image_profile']
                ]
            );
            $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM users ORDER BY id DESC LIMIT 1');
            $id = $lastInsertedId['id'];

            echo "<script type='text/javascript'>alert('Hemos enviado un email de confirmación a su correo para activar la cuenta.');</script>";

            ini_set('sendmail_from', "nuria.gomezpiedrafita@gmail.com");
            ini_set("SMTP", $data['email']);
            ini_set('smtp_port', 25);

            $message = 'Gracias por registrarte en Pwgram. Acceda al link siguiente http://silexapp.dev/users/validation/'.$id;
            mail($data['email'], 'Confirmacion Pwgram', $message);


            //$url = '/users/get/'.$id;
            //return new RedirectResponse($url);
            return $response;
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
                $pass = md5($data['password']);

                $match = $app['db']->fetchAssoc("SELECT * FROM users WHERE (username = '$login' OR email = '$login')  AND password = '$pass'");
                echo var_dump($match['id']);
                if($match == true){
                    $this->logSession($app,$match['id']);
                    $url = '/';
                    return new RedirectResponse($url);
                }else{
                    $response->setStatusCode(Response::HTTP_OK);
                    $content = $app['twig']->render('loginUser.twig',array(
                        'form'=> $form->createView(),
                        'logged' => $match
                    ));
                    $response->setContent($content);

                    return $response;
                }
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

    public function addImg(Application $app, Request $request)
    {
        $response = new Response();
        $ok = true;
        $data = array(
            'Private' =>  false,
        );

        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('Title', TextType::class, array(
                'constraints' => array(
                    'constraints' => new CorrectLogin(
                        array(
                            'message' => 'Invalid Title: Must contain alphanumeric values, and less than 20 characters (not HTML syntax)'
                        )
                    )
                )
            ))
            ->add('New_Image', FileType::class, array(
                'required' => true
            ))
            ->add('Private', CheckboxType::class, array(
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
                /*
                 * LOGIC OF THE ADDIMG, SHOULD RETURN OK IF WORKS
                 */
                //ToDo: Find User ID
                /*$app['db']->insert('images',[
                        'title' => $data['Title'],
                        //'user_id' => $data['email'],
                        'img_path' => $data['New_Image'],
                        'visits' => 0,
                        'private' => $data['Private'],
                    ]
                );*/
                /*
                 */
                var_dump($app['user']->id);
                $ok = true; //DEBUG MODE
                if($ok == true){
                    $url = '/';
                    return new RedirectResponse($url);
                }else {
                    $response->setStatusCode(Response::HTTP_OK);
                    $content = $app['twig']->render('addImg.twig', array(
                        'form' => $form->createView(),
                        'ok' => $ok
                    ));
                    $response->setContent($content);

                    return $response;
                }
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
        $content = $app['twig']->render('addImg.twig',array(
            'form'=> $form->createView(),
            'ok' => $ok
        ));
        $response->setContent($content);

        return $response;
    }
}