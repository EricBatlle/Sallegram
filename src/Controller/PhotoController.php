<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 13/05/2017
 * Time: 19:28
 */

namespace SilexApp\Controller;


use DateTime;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use SilexApp\Model\Entity\User;
use SilexApp\Model\Entity\UserType;
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


class PhotoController extends BaseController
{

    public function removePhoto (Application $app, $id){

        $response = new Response();

        try {
            $app['db']->exec("DELETE FROM images WHERE id = $id");
            $url = '/users/photos';
            return new RedirectResponse($url);
        } catch (Exception $e){
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

    public function editPhoto (Application $app, $id, Request $request){

        $response = new Response();

        $sql = "SELECT * FROM images WHERE id = $id";

        //$id = $app['session']->get('id');

        $image = $app['db']->fetchAssoc($sql); //llamando al servicio

        $ok = true;

        $data = array(
            'Title' => $image['title'],
            'Private' => boolval($image['private']),
        );

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
                'required' => false,
                'attr' => ['class'=>'form_thumbnail']
            ))

            ->add('Private', CheckboxType::class, array(
                'required' => false,
            ))
            ->add('submit',SubmitType::class, [
                'label' => 'Save',
            ])
            ->getForm();


        $form->handleRequest($request);

        if($form->isValid()){

            $data = $form->getData();
            //IMAGE
            $dir = 'assets/uploads';

            try{
                if($data['New_Image'] == NULL) {
                    $app['db']->update('images', [
                        'title' => $data['Title'],
                        'private' => $data['Private']],
                        array('id' => $id)
                    );

                } else {
                    $filename = $data['New_Image'];
                    $filename->move($dir, $filename->getClientOriginalName());

                    $app['db']->update('images',[
                        'title' => $data['Title'],
                        'img_path' => $filename->getClientOriginalName(),
                        'private' => $data['Private'],
                        'created_at' => date('Y-m-d H:i:s')],
                        array('id' => $id)
                    );
                }

                $url = '/users/photos';
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
        $content = $app['twig']->render('editImg.twig',array(
            'form'=> $form->createView(),
            'ok' => $ok,
            'photos' => $image,
        ));
        $response->setContent($content);

        return $response;
    }

    public function viewPhoto (Application $app, $id){

        $response = new Response();

        //Check if it's public

        $image = $app['db']->fetchAssoc("SELECT * FROM images WHERE id = '$id'"); //llamando al servicio
        if($image['private'] == 0){ //Public
            //Incrementar el num de visites de la imatge
            $app['db']->update('images',[
                'visits' => $image['visits']+1],
                array('id' => $id)
            );
            //Display INFO
            //Nom del user que l'ha pujat (ha de ser un link al seu profile)
            $userId = $image['user_id'];

            $user = $app['db']->fetchAssoc("SELECT * FROM users WHERE id='$userId'"); //llamando al servicio
            //Titol
            //Imatge (400x300)


            //Dies que han pasat des de la pujada (dia actual - dia pujada)
            $actual_date = date("d-m-Y", time());
            $image_date = date("d-m-Y", (new DateTime($image['created_at']))->getTimestamp());

            $actual = explode ("-", $actual_date);
            $d_image = explode ("-", $image_date);

            $t1 = mktime(0,0,0, $actual[1], $actual[0], $actual[2]);
            $t2 = mktime(0,0,0, $d_image[1], $d_image[0], $d_image[2]);

            $seg = $t1 - $t2;

            $dias = $seg/(60*60*24);

            //Comentaris publicats de la imatge (default 3)
            $comments = $app['db']->fetchAll("SELECT * FROM comments WHERE image_id ='$id'"); //llamando al servicio

            //Afegir botó AJAX per carregar-ne 3 més
            //Num Visits
            //Num Likes
        }else{ //Private
            //Redirect to Error 403
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $content = $app['twig']->render('error.twig',[
                'errors' => [
                    'unexpected' => 'An error has ocurred, please try it again later'
                ]
            ]);

            $response->setContent($content);
            return $response;
        }

        $response->setStatusCode(Response::HTTP_OK);
        $content = $app['twig']->render('showImg.twig',array(
            'user' => $user['username'],
            'image' => $image,
            'interval' => $dias,
            'comments' => $comments
        ));
        $response->setContent($content);

        return $response;
    }

    public function addMoreTop5(Application $app, Request $request, $clicks)
    {
        $ok = false;

        //Mirar si HAY más imagenes de 5
        $match = $app['db']->fetchAll("SELECT images.*, users.username FROM images, users WHERE user_id = users.id ORDER BY visits");
        $offset = 5*$clicks;

        if(count($match) > $offset){
            $ok = true;
            $session = $app['session']->has('id');
            //Mirar el valor de clicks de más imágenes
            $offset = 5*$clicks;

            //Sacar de la DB tantos comentarios de la imagen LIMIT Comentarios
            $images = $app['db']->fetchAll("SELECT images.*, users.username FROM images, users WHERE user_id = users.id ORDER BY visits DESC LIMIT 5 OFFSET $offset");


            //Devolverlos al javascript
            return new JsonResponse([
                0 => $ok,
                1 => $images,
                2 => $session
            ]);
        }else{//si tiene menos de 5
            //No hacer nada y devolver un false
            return new JsonResponse([
                0 => $ok
            ]);
        }

    }

    public function addMoreLast5(Application $app, Request $request, $image_id, $clicks)
    {

    }
}