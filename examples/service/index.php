<?php
require '../bootstrap.php';

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
    $result = $app->request->getBody();
    return $result;
});

$app->post('/cached/{name}', function($name = '') use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->consumes(['json', 'xml', 'form', 'text'])->produces(['json', 'xml', 'form', 'text', 'html']);
    $result = $app->serverCache($app->request->getBody(), function($data) use ($app) {
        $data['xxxx'] = 'yyy';
        sleep(1);
        return $data;
    }, 20);
    return $result;
});


$app->get('/cached', function() use ($app) {
    //produces or consumes calls to check if the client sends or expects extension
    $app->produces(['json', 'xml', 'form', 'text', 'html']);
    $result = $app->serverCache($app->request->getBody(), function($data) use ($app) {
        return array('myresult' => 'ok');
    }, 15);
    return $result;
});

$app->post('/all', function() use ($app) {
    //without consumes and/or produces, accept all registered types
    //$app->producesExcept(['yaml']);
    $return = $app->request->getBody();
    return $return;
});

$app->options('(/.*)*', function() use ($app) {
    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');
    }
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, HEAD, OPTIONS");
    }

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    }
});

try {
    $app->handle();
} catch (\Exception $e) {
    echo $e->getMessage();
}

