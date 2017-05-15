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

return $app;