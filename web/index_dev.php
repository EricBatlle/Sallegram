<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 29/03/2017
 * Time: 19:12
 */

ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../app/app.php';
require __DIR__.'/../app/config/dev.php';
require __DIR__.'/../app/config/routes.php';
$app->run();