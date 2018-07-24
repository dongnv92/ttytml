<?php
require_once 'includes/core.php';
switch ($act){
    case 'update':
        $seri       = $_GET['seri'];
        $type       = $_GET['type'];
        $pin        = $_GET['pin'];
        $content    = 'Seri: '.$seri.' | Type: '.$type.' | Pin: '.$pin;
        mysqli_query($db_connect, 'UPDATE `dong_config` SET `config_value` = "'. $content .'" WHERE `config_name` = "iot"');
        $query_config = mysqli_query($db_connect, "SELECT * FROM `dong_config`;");
        $table_config = array();
        while ($table_config_res = mysqli_fetch_array($query_config)) $table_config[$table_config_res[1]] = $table_config_res[2];
        mysqli_free_result($query_config);
        echo $table_config['iot'];
        break;
    default:
        echo $table_config['iot'];
        break;
}