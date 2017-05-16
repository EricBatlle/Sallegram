<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 14/05/2017
 * Time: 23:24
 */

namespace SilexApp\Controller;


use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Imagick\Imagine;
use SilexApp\Controller\Validations\CorrectComment;
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


class LikeController extends BaseController
{

    public function like(Application $app, Request $request,$status ,$id_image)
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        $id = $app['session']->get('id');
        $image = $app['db']->fetchAssoc("SELECT * FROM images where id = $id_image");

        try{


            if($status !='Like'){
                //ELIMINAR AQUÍ
                $likes = $image['likes'] - 1;
                $app['db']->exec("DELETE FROM likes WHERE image_id = $id_image and user_id = $id");
                $do = 0;
            }else{
                $likes = $image['likes'] + 1;

                $do = 1;
                $app['db']->insert('likes', [
                        'image_id' => $id_image,
                        'user_id' => $id,
                        'liked' => $do
                    ]
                );

                $app['db']->insert('notifications', [
                        'img_id' => $id_image,
                        'user_id' => $app['session']->get('id'),
                        'type' => 'l',
                    ]
                );
            }
            $app['db']->update('images',[
                'likes' =>  $likes],
                array('id' => $id_image)
            );

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
        //Devolverlos al javascript
        return new JsonResponse([
            0 => $do
        ]);
    }

    public function removeNotification (Application $app, $id){
        $response = new Response();

        try {
            $app['db']->exec("DELETE FROM notifications WHERE id = $id");
            $url = '/notifications';
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

    public function showNotifications (Application $app)
    {
        $response = new Response();

        $id = $app['session']->get('id');

        $notifications = $app['db']->fetchAll("SELECT users.username, notifications.*, images.title, images.img_path FROM users, notifications, images WHERE images.id = notifications.img_id and users.id = images.user_id and images.user_id = $id ORDER BY notifications.time DESC ");


        $content = $app['twig']->render('notifications.twig', [
            'notifications' => $notifications]);
        $response->setContent($content);

        return $response;
    }

}