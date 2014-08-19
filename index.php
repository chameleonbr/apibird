<?php
require 'vendor/autoload.php';

$di = new \Phalcon\DI\FactoryDefault();
$app = new \ApiBird\Service($di);
$app->registerExtensions([
    'json' => '\\ApiBird\\Extension\\Json',
    'xml' => '\\ApiBird\\Extension\\Xml'
]);

$app->get('/', function() use ($app) {
    $app->produces(['json', 'xml']);
    $return = ['xpto' => 123];
    return $return;
});


$app->post('/', function() use ($app) {
    $app->consumes(['json', 'xml'])->produces(['json', 'xml']);
    $val = $app->getBody();
    $return = $val;
    return $return;
});

try {
    $app->handle();
} catch (\ApiBird\InvalidTypeException $e) {
    echo $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}