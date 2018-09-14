<?php
require_once 'includes/core.php';
    $seri       = $_GET['seri'];
    $type       = $_GET['type'];
    $pin        = $_GET['pin'];
    if($seri && $type && $pin){
        $data = array(
            'iotbutton_seri'    => $seri,
            'iotbutton_type'    => $type,
            'iotbutton_pin'     => $pin,
            'iotbutton_time'    => _CONFIG_TIME
        );
        insertGlobal('dong_iotbutton', $data);
        echo 'OK';
    }else{
        echo 'NOT OK';
    }