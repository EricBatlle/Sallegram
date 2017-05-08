<?php
/**
 * Created by PhpStorm.
 * User: Erik
 * Date: 29/03/2017
 * Time: 19:06
 */

use Silex\Provider\FormServiceProvider;

$app->register(new Silex\Provider\TwigServiceProvider(),array(
    'twig.path' => __DIR__ .'/../../src/View/templates',
));

$app->register(new Silex\Provider\AssetServiceProvider(),array(
    'assets.version' => 'v1',
    'assets.version_format' => '%s?Version=%s',
    'assets.named_packages' => array(
        'css' => array('base_path' => '/assets/css'),
        'js' => array('base_path' => '/assets/js'),
        'images' => array('base_urls' => array('http://silexapp.dev/assets/img')),
    ),
));

$app->register(new \SilexApp\Providers\HelloServiceProvider(), array(
    'hello.default_name' => 'Eric',
));

$app->register(new \Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'pwgram',
        'user' => 'root',
        'password' => ''
    ),
));

$app->register(new \Silex\Provider\SessionServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new \Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'translator.domains' => array(),
));
$app->register(new Silex\Provider\LocaleServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider());
