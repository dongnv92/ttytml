<?php
require_once 'includes/core.php';

foreach (getGlobalAll(_TABLE_GROUP, '', array('query' => 'SELECT * FROM `'. _TABLE_TASKS .'` WHERE `task_from` = 1 AND `task_end` < "'. date('Y-m-d', time()) .'" AND `task_status` != 2')) AS $task){
    echo $task['id'].'<br />';
}