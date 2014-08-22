<?php

require '../../vendor/autoload.php';

// DI is necessary to Service Class
$di = new \Phalcon\DI\FactoryDefault();

//now register the extensions globally
$di->set('apibird', function() {
    $api = new \ApiBird\ExtensionProvider();
    $api->registerExtensions([
        'json' => '\\ApiBird\\Extension\\Json',
        'xml' => '\\ApiBird\\Extension\\Xml',
        'form' => '\\ApiBird\\Extension\\Form',
        'html' => '\\ApiBird\\Extension\\Html',
        'text' => '\\ApiBird\\Extension\\Text',
    ]);
    $api->setDefaultContentType('json');
    $api->setDefaultAccept('form');
    return $api;
}, true);

// create api bird
$app = new \ApiBird\Service($di);

$app->get('/', function() use ($app) {
    //produces or consumes calls to check if the client sends expected extension
    $app->produces(['json', 'xml', 'html', 'form']);
    $return = ['xpto' => 123];
    //array returned is converted to Accept header extension
    return $return;
});


$app->post('/', function() use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->consumes(['json', 'xml', 'form', 'text'])->produces(['json', 'xml', 'form', 'text', 'html']);
    $val = $app->request->getBody();
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