<?php
require 'vendor/autoload.php';

$di = new \Phalcon\DI\FactoryDefault();
$app = new \ApiBird\Service($di);
$app->registerHandlers([
    'json' => '\\ApiBird\\Handler\\Json',
    'xml' => '\\ApiBird\\Handler\\Xml'
]);

$app->get('/', function() use ($app) {
    $app->produces(['json', 'xml']);
    $retorno = ['xpto' => 123];
    return $retorno;
});


$app->post('/', function() use ($app) {
    $app->consumes(['json', 'xml'])->produces(['json', 'xml']);
    $val = $app->getData();
    $retorno = $val;
    return $retorno;
});

try {
    $app->handle();
} catch (\ApiBird\InvalidTypeException $e) {
    echo $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}