<?php
// DEBUG XHPROF
if (isset($_REQUEST['DEBUG_MODE']) && $_REQUEST['DEBUG_MODE'] > 0) {
    xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
}