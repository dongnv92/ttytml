<?php
$start_time = microtime(true);
require_once 'includes/core.php';

$num    = 268;
$pagi   = 200;
$plus   =  $num%$pagi;
$pages  = ceil($num/$pagi);

echo "$num - $pages";

echo '<br />Load in '.(number_format(microtime(true) - $start_time, 2)).' seconds';
