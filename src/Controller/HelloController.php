<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 03/04/2017
 * Time: 15:50
 */
namespace  SilexApp\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HelloController
{
    public function indexAction(Application $app, $name)
    {
        return $app['hello']($name);
    }

    public function addAction(Application $app, $num1, $num2)
    {
        return "The result is ".$app['calc']->add($num1,$num2);
    }
}