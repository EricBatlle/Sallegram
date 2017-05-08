<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 07/05/2017
 * Time: 21:42
 */

namespace SilexApp\Model\Entity;

//use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Doctrine\Common\Persistence\Mapping as ORM;

class User
{

    public $id;
    private $username;
    private $email;
    private $birthdate;
    private $password;
    private $img_path;
    private $active;

    function __construct1($id,$username,$email,$birthdate,$password,$img_path,$active)
    {
         $this->$id = $id;
         $this->$username = $username;
         $this->$email = $email;
         $this->$birthdate = $birthdate;
         $this->$password = $password;
         $this->$img_path = $img_path;
         $this->$active = $active;
    }

    function __construct()
    {

    }

    /**
     * @var UploadedFile
     */
    private $brochure;

    public function getBrochure()
    {
        return $this->brochure;
    }

    public function setBrochure($brochure)
    {
        $this->brochure = $brochure;

        return $this;
    }
}