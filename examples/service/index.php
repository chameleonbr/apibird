<?php

// DEBUG XHPROF
if (isset($_REQUEST['DEBUG_MODE']) && $_REQUEST['DEBUG_MODE'] > 0) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}

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
        'csv' => '\\ApiBird\\Extension\\Csv',
        'multipart' => '\\ApiBird\\Extension\\Multipart',
    ]);
    $api->setDefaultProduces('json');
    $api->setDefaultConsumes('form');
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
    $return = $app->request->getBody();
    return $return;
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
} catch (\ApiBird\InvalidTypeException $e) {
    echo $e->getMessage();
} catch (\Exception $e) {
    echo $e->getMessage();
}

if (isset($_REQUEST['DEBUG_MODE']) && $_REQUEST['DEBUG_MODE'] > 0) {
    $xhprof_data = xhprof_disable();

    $type = 'RECEITAPR';
    if (isset($_REQUEST['TYPE'])) {
        $type = $_REQUEST['TYPE'];
    }

    $XHPROF_ROOT = "/srv/www/htdocs";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, $type);

    echo "<a href=\"/xhprof_html/index.php?run={$run_id}&source={$type}\">aqui</a>\n";
}
