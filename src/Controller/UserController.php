<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 19/04/2017
 * Time: 18:56
 */

namespace SilexApp\Controller;

use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\File\File;
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
use Symfony\Component\HttpFoundation\JsonResponse;
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

    public function publicProfile(Application $app, $id){
        $response = new Response();

//        $photos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id ORDER BY created_at ASC");
//        $photos1 = NULL;

        if (isset($_POST['options'])) {
            switch ($_POST['options']){
                case 1:
                    $photos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id ORDER BY created_at ASC");
                    $photos1 = NULL;
                    $countphotos = $photos;
                    break;

                case 2:
                    $photos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id ORDER BY likes DESC");
                    $photos1 = NULL;
                    $countphotos = $photos;
                    break;

                case 3:
                    $countphotos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id");
                    $photos = $app['db']->fetchAll("SELECT images.id, images.title, images.img_path, count(comments.id) FROM images, comments WHERE images.id = image_id and images.user_id = $id GROUP BY image_id ORDER BY count(comments.id) DESC");
                    $photos1 = $app['db']->fetchAll("SELECT * FROM images WHERE images.user_id = $id and id NOT IN (SELECT image_id FROM comments, images WHERE image_id = images.id)");
                    break;

                case 4:
                    $photos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id ORDER BY visits DESC");
                    $photos1 = NULL;
                    $countphotos = $photos;
            }
        } else {
            $photos = $app['db']->fetchAll("SELECT * FROM images WHERE user_id = $id ORDER BY created_at ASC");
            $photos1 = NULL;
            $countphotos = $photos;
            $_POST['options'] = '1';
        }

        $profile = $app['db']->fetchAssoc("SELECT * FROM users WHERE id = $id");

        $userComments = $app['db']->fetchAll("SELECT * FROM comments, images WHERE image_id = images.id and images.user_id = $id");

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('publicProfile.twig',array(
            'profile' => $profile,
            'photos' => $photos,
            'photos1' => $photos1,
            'count' => $countphotos,
            'comments' => $userComments,
            'option' => $_POST['options']
        ));
        $response->setContent($content);

        return $response;

    }

    public function userPhotos (Application $app){
        $response = new Response();

        $id = $app['session']->get('id');

        $sql = "SELECT * FROM images WHERE user_id = '$id' ORDER BY created_at DESC";
        $id = $app['session']->get('id');
        $photos = $app['db']->fetchAll($sql);

        $content = $app['twig']->render('userPhotos.twig',[
            'photos' => $photos,
        ]);

        $response->setContent($content);

        return $response;
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

                $this->logSession($app,$user['id'],$user['username'],$user['img_path']);
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
        $response = new Response();

        $sql = "SELECT * FROM users WHERE id = ?";
        $id = $app['session']->get('id');
        //var_dump($app['session']->get('id'));
        $user = $app['db']->fetchAssoc($sql, array((int)$id)); //llamando al servicio

        $data = array(
            'name' => $user['username'],
            //'image_profile' => $user['img_path'],
            'image_profile' => new File('assets/uploads/'.$user['img_path'])
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
                'required' => false,
                'label_attr' => array('file_path' => 'test'),
                'attr' => ['class'=>'form_thumbnail']

            ))

            ->add('submit',SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();
        $form->handleRequest($request);


        if($form->isValid()){
            $data = $form->getData();

            if($data['image_profile'] != NULL){
                $dir = 'assets/uploads';

                /** @var UploadedFile $someNewFilename */
                $someNewFilename = $data['image_profile'];

                $filename = $someNewFilename->getClientOriginalName();
                $someNewFilename->move($dir, $filename);

                $app['session']->set('img',$filename);
            }

            try{
                if($data['image_profile'] == NULL){
                    $app['db']->update('users',[
                        'username' => $data['name'],
                        'birthdate' => (string)$data['birthdate']->format('Y-m-d'),
                        'password' => md5($data['password'])],
                        array('id' => $app['session']->get('id'))
                    );
                } else {
                    $app['db']->update('users', [
                        'username' => $data['name'],
                        'birthdate' => (string)$data['birthdate']->format('Y-m-d'),
                        'password' => md5($data['password']),
                        'img_path' => $filename],
                        array('id' => $app['session']->get('id'))
                    );
                }

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
        }

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('showUser.twig',array(
            'form'=> $form->createView(),
            'photo' => $user['img_path'])
        );
        $response->setContent($content);

        return $response;
    }

    public function registerUser (Application $app, Request $request)
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
                'required' => false,
                'label_attr' => ['id' => 'imgInp'],
                'attr' => ['class'=>'form_thumbnail']
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
            /** @var UploadedFile $someNewFilename */
            $someNewFilename = $data['image_profile'];
            if($data['image_profile'] == null){
                //$filename = "default.jpg";
                $someNewFilename = new File('assets/uploads/default.jpg');
                $filename = $someNewFilename->getFilename();
            }else{
                $filename = $someNewFilename->getClientOriginalName();
            }

            $someNewFilename->move($dir, $filename);

            try{
                $app['db']->insert('users',[
                    'username' => $data['name'],
                    'email' => $data['email'],
                    'birthdate' => $data['birthdate']->format('Y-m-d'),
                    'password' => md5($data['password']),
                    'img_path' => $filename
                ]
            );
                $lastInsertedId = $app['db']->fetchAssoc('SELECT id FROM users ORDER BY id DESC LIMIT 1');
                $id = $lastInsertedId['id'];

                $message = 'Gracias por registrarte en Pwgram. Acceda al link siguiente http://silexapp.dev/users/validation/'.$id;
                mail($data['email'], 'Confirmacion Pwgram', $message);

                $content = $app['twig']->render('emailSend.twig');
                $response->setContent($content);
                return $response;

            }catch(Exception $e){
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $content = $app['twig']->render('registerUser.twig',[
                    'errors' => [
                        'unexpected' => 'An error has ocurred, please try it again later'
                    ]
                ]);
                $response->setContent($content);
                return $response;
            }
        }

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('registerUser.twig',array('form'=> $form->createView()));
        $response->setContent($content);

        return $response;
    }

    public function loginUser(Application $app, Request $request)
    {
        $response = new Response();
        $match = true;
        $data = array(
            'user-email' => 'Yourname',
        );

        /** @var Form $form */
        $form = $app['form.factory']->createBuilder(FormType::class, $data)
            ->add('user-email', TextType::class, array(
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
                $login = $data['user-email'];
                $pass = md5($data['password']);

                $match = $app['db']->fetchAssoc("SELECT * FROM users WHERE active = 1 and (username = '$login' OR email = '$login')  AND password = '$pass'");
                //echo var_dump($match['id']);
                if($match == true){
                    $this->logSession($app,$match['id'],$match['username'],$match['img_path']);
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
                'required' => true,
                'attr' => ['class'=>'form_thumbnail']
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
            //IMAGE
            $dir = 'assets/uploads';
            //ToDo: If image=null -> take default image
            /** @var UploadedFile $filename */
            $filename = $data['New_Image'];

            $filename->move($dir, $filename->getClientOriginalName());

            /////RESIZE//////
            //$filename_400 = $this->resizeAndCopy($filename,$dir,400,300);
            /*$filename_100 = $this->resizeAndCopy($filename,$dir,100,100);
            */
            $filename_100 = $app['image_manager']->resizeAndCopy($filename,$dir,100,100);
            $filename_400 = $app['image_manager']->resizeAndCopy($filename,$dir,400,300);

            try{
                $app['db']->insert('images',[
                        'user_id' => $app['session']->get('id'),
                        'title' => $data['Title'],
                        'img_path' => $filename->getClientOriginalName(),
                        'visits' => 0,
                        'private' => $data['Private'],
                        'created_at' => date('Y-m-d H:i:s')
                    ]
                );
                $pathMainImage = $filename->getClientOriginalName();
                $mainImage = $app['db']->fetchAssoc("SELECT * FROM images WHERE img_path='$pathMainImage'");
                $app['db']->insert('thumbs',[
                        'image_id' => $mainImage['id'],
                        'user_id' => $app['session']->get('id'),
                        'title' => $data['Title'],
                        'img_path' => $filename_400,
                        'width' => 400
                    ]
                );

                $app['db']->insert('thumbs',[
                        'image_id' => $mainImage['id'],
                        'user_id' => $app['session']->get('id'),
                        'title' => $data['Title'],
                        'img_path' => $filename_100,
                        'width' => 100
                        ]
                );

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
        }

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('addImg.twig',array(
            'form'=> $form->createView(),
            'ok' => $ok
        ));
        $response->setContent($content);

        return $response;
    }
/*
    public function resizeAndCopy($filename,$dir,$nuevo_ancho,$nuevo_alto){
        $imgFilename = $filename->getClientOriginalName();
        $nombreFichero = $dir.'/'.$imgFilename;

        $thumb = imagecreatetruecolor($nuevo_ancho,$nuevo_alto);
        $origen = imagecreatefromjpeg($nombreFichero); //ToDo: better from string?

        $width = imagesx($origen);
        $height = imagesy($origen);

        imagecopyresized($thumb,$origen,0,0,0,0,$nuevo_ancho,$nuevo_alto,$width,$height);
        $newNameFile = $nombreFichero.$nuevo_ancho.'x'.$nuevo_alto.'.jpeg';
        imagejpeg($thumb,$newNameFile);
        return $newNameFile;
    }*/

    public function allComments(Application $app, Request $request)
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        //Get all user comments
        $idUser = $app['session']->get('id');
        $userComments = $app['db']->fetchAll("SELECT * FROM comments WHERE user_id='$idUser'");

        $content = $app['twig']->render('/allComments.twig',array(
            //'form'=> $form->createView(),
            'comments' => $userComments
        ));
        $response->setContent($content);

        return $response;
    }

}