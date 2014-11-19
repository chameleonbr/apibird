<?php

require 'vendor/autoload.php';
// DI is necessary to Service Class
$di = new \Phalcon\DI\FactoryDefault();

//now register the extensions globally
$di->set('apibird', function() {
    $api = new \ApiBird\ServiceProvider();
    $api->registerExtensions([
        'json' => '\\ApiBird\\Extension\\Json',
        'xml' => '\\ApiBird\\Extension\\Xml',
        'form' => '\\ApiBird\\Extension\\Form',
        'html' => '\\ApiBird\\Extension\\Html',
        'text' => '\\ApiBird\\Extension\\Text',
        'csv' => '\\ApiBird\\Extension\\Csv',
        'multipart' => '\\ApiBird\\Extension\\Multipart',
    ]);
    $api->setDefaultProduces('json');
    $api->setDefaultConsumes('form');
    return $api;
}, true);
// this enables serverCache method
$di->set('cache', function() {
    $frontCache = new Phalcon\Cache\Frontend\Data([
        "lifetime" => 3600
    ]);
    $cache = new Phalcon\Cache\Backend\Apc($frontCache, [
        'prefix' => 'datacache'
    ]);
    return $cache;
}, true);

// create api bird
$app = new \ApiBird\Micro($di);

$app->get('/', function() use ($app) {
    //produces or consumes calls to check if the client sends expected extension
    $app->produces(['json', 'xml', 'html', 'form']);
    $return = ['xpto' => 123];
    //array returned is converted to Accept header extension
    return $app->response->ok($return);
});

$app->post('/', function() use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->consumes(['json', 'xml', 'form', 'text'])->produces(['json', 'xml', 'form', 'text', 'html']);
    $result = $app->request->getBody();
    return $app->response->created($result);
});

$app->post('/cached/{name}', function($name = '') use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->consumes(['json', 'xml', 'form', 'text'])->produces(['json', 'xml', 'form', 'text', 'html']);
    $result = $app->cache($app->request->getBody(), function($data) use ($app) {
        return $data;
    }, 20);
    return $app->response->ok($result);
});

$app->get('/cached', function() use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->produces(['json', 'xml', 'form', 'text', 'html']);
    $result = $app->cache($app->request->getBody(), function($data) use ($app) {
        return array('myresult' => 'ok');
    }, 15);
    return $app->response->ok($result);
});

$app->post('/all', function() use ($app) {
    //without consumes and/or produces, accept all registered types
    //$app->producesExcept(['yaml']);
    $return = $app->request->getBody();
    return $app->response->ok($return);
});

//Enable CORS

$app->options('(/.*)*', function() use ($app) {
});

try {
    $app->handle();
} catch (\Exception $e) {
    echo $e->getMessage();
}

