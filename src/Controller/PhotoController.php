<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 13/05/2017
 * Time: 19:28
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
                'required' => false
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

}