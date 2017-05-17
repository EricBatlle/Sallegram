<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 18/05/2017
 * Time: 1:00
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

class ProfileController extends BaseController
{

    public function publicProfile(Application $app, $id){
        $response = new Response();
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
                $app['session']->set('name',$data['name']);

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
        $content = $app['twig']->render('showUser.twig',array(
                'form'=> $form->createView(),
                'photo' => $user['img_path'])
        );
        $response->setContent($content);

        return $response;
    }

}