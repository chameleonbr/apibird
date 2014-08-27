<?php

if (isset($_REQUEST['DEBUG_MODE']) && $_REQUEST['DEBUG_MODE'] > 0) {
    $xhprof_data = xhprof_disable();

    $type = 'APIBIRD';
    if (isset($_REQUEST['TYPE'])) {
        $type = $_REQUEST['TYPE'];
    }

    $XHPROF_ROOT = "/srv/www/htdocs";//docroot of xhprof lib
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
    include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

    $xhprof_runs = new XHProfRuns_Default();
    $run_id = $xhprof_runs->save_run($xhprof_data, $type);

    echo "<a href=\"/xhprof_html/index.php?run={$run_id}&source={$type}\">aqui</a>\n";
}
