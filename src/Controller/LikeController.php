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

    public function like(Application $app, Request $request, $id_image, $id_user)
    {

    }

    public function showNotifications (Application $app)
    {
        $response = new Response();

        $content = $app['twig']->render('notifications.twig');
        $response->setContent($content);

        return $response;
    }

}