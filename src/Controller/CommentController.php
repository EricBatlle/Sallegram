<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 13/05/2017
 * Time: 19:24
 */

namespace SilexApp\Controller;


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



class CommentController extends BaseController
{

    public function addComment(Application $app, Request $request, $id)
    {
        return new JsonResponse([
            0 => 'image1',
            1 => 'image2',
            3 => 'image3',
        ]);
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);
        $idUser = $app['session']->get('id');
        //Find if the user has commented on the img
        $match = $app['db']->fetchAssoc("SELECT * FROM comments WHERE user_id='$idUser' AND image_id='$idUser'");
        var_dump($match);
        if($match == true){
            //Can't add the comment
            //ToDo: what to do? Redirect to home again?
            $url = '/';
            return new RedirectResponse($url);

        }else{
            //Add the comment to the image on db
            try{
                $app['db']->insert('comments',[
                        'user_id' => $app['session']->get('id'),
                        //ToDo: Como recupero la info del form? :S
                        /*                        'comment' => $data['Title'],*/
                        'image_id' => $id
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

        return $response;
    }

    public function removeComment(Application $app, Request $request, $id)
    {
        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        $userComment = $app['db']->exec("DELETE FROM comments WHERE id='$id'");

        $url = '/allComments';
        return new RedirectResponse($url);
    }


}