<?php
/**
 * Created by PhpStorm.
 * User: nguye
 * Date: 13/04/2018
 * Time: 23:05
 */
require_once '../includes/core.php';
header('Content-Type: text/html; charset=utf-8');

if(!$token){
    echo '<center><h1>THIẾU MÃ TOKEN</h1>Thiếu mã Token, để thực hiện thao tác này, vui lòng liên hệ với BQT để lấy mã TOKEN. <a href="'. _URL_REFERER .'">Quay lại</a> </center>';
    exit();
}

if(!checkToken($token)){
    echo '<center><h1>MÃ TOKEN KHÔNG ĐÚNG HOẶC HẾT HẠN</h1>Mã Token không đúng hoặc hết hạn, để thực hiện thao tác này, vui lòng liên hệ với BQT để lấy mã TOKEN. <a href="'. _URL_REFERER .'">Quay lại</a> </center>';
    exit();
}
$download   = getGlobal('dong_files', array('id' => $id));
$file       = '../'.$download['files_url'];
if (file_exists($file)) {
    $check_download = $db->from(_TABLE_GROUP)->where(array('group_type' => 'file_download', 'group_id' => $download['id'], 'group_users' => $user_id))->fetch_first();
    if($check_download){
        $db->where('id', $check_download['id'])->update(_TABLE_GROUP, array('group_value' => ($check_download['group_value'] + 1)));
    }else{
        $data_download = array(
            'group_type'    => 'file_download',
            'group_id'      => $download['id'],
            'group_value'   => 1,
            'group_users'   => $user_id,
            'group_time'    => _CONFIG_TIME
        );
        $db->insert(_TABLE_GROUP, $data_download);
    }
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
}else{
    echo'error: '.$file;
}