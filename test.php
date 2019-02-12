<?php
$start_time = microtime(true);
require_once 'includes/core.php';

echo 'Count: '.$function->createParameter(array('page' => '{page}'));

echo '<br />Load in '.(number_format(microtime(true) - $start_time, 2)).' seconds';