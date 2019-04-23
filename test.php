<?php
$start_time = microtime(true);
require_once 'includes/core.php';

echo 'Hello World';


echo '<br />Load in '.(number_format(microtime(true) - $start_time, 2)).' seconds';
