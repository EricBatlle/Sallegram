<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 29/03/2017
 * Time: 19:07
 */

use Silex\Application;
use SilexApp\Model\Entity\User;

$app = new Application();
$app['app.name'] = 'SilexApp';
$app['calc'] = function (){
    return new \SilexApp\Model\Services\Calculator();
};
$app['user'] = new User();
return $app;