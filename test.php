<?php
require_once 'includes/core.php';
$date = '2018/12/12';
if(!checkDateFormart($date)){
    echo 'Not OK';
}else{
    echo 'OK';
}