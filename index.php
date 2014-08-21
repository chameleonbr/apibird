<?php
require 'vendor/autoload.php';

// DI is necessary to Service Class
$di = new \Phalcon\DI\FactoryDefault();

$app = new \ApiBird\Service($di);
//now register the extensions globally
$app->registerExtensions([
    'json' => '\\ApiBird\\Extension\\Json',
    'xml' => '\\ApiBird\\Extension\\Xml',
    'form' => '\\ApiBird\\Extension\\Form'
]);

$app->get('/', function() use ($app) {
    //produces or consumes calls to check if the client sends expected extension
    $app->produces(['json', 'xml']);
    $return = ['xpto' => 123];
    //array returned is converted to Accept header extension
    return $return;
});


$app->post('/', function() use ($app) {
    //produces or consumes calls to check if the client sends expected extension
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